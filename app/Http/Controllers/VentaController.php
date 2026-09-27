<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VentaController extends Controller
{
    /**
     * Muestra el historial de ventas.
     */
    public function index(Request $request): View
    {
        $ventas = Venta::withCount('detalles')
            ->when(
                $request->fecha_inicio,
                function ($consulta, $fechaInicio) {
                    $consulta->whereDate(
                        'fecha',
                        '>=',
                        $fechaInicio
                    );
                }
            )
            ->when(
                $request->fecha_fin,
                function ($consulta, $fechaFin) {
                    $consulta->whereDate(
                        'fecha',
                        '<=',
                        $fechaFin
                    );
                }
            )
            ->when(
                $request->buscar,
                function ($consulta, $buscar) {
                    $consulta->where(
                        'numero_venta',
                        'like',
                        "%{$buscar}%"
                    );
                }
            )
            ->latest('fecha')
            ->paginate(15)
            ->withQueryString();

        return view(
            'ventas.index',
            compact('ventas')
        );
    }

    /**
     * Muestra el formulario para registrar una venta.
     */
    public function create(): View
    {
        $productos = Producto::with('categoria')
            ->where('estado', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view(
            'ventas.create',
            compact('productos')
        );
    }

    /**
     * Registra una venta y descuenta el stock.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $datos = $request->validate([
            'productos' => [
                'required',
                'array',
                'min:1',
            ],

            'productos.*.producto_id' => [
                'required',
                'integer',
                'distinct',
                'exists:productos,id',
            ],

            'productos.*.cantidad' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $venta = DB::transaction(function () use ($datos) {
            $venta = Venta::create([
                'user_id' =>
                    auth()->id(),

                'numero_venta' =>
                    'TMP-' . Str::upper(
                        Str::random(20)
                    ),

                'fecha' =>
                    now(),

                'subtotal' =>
                    0,

                'total' =>
                    0,

                'estado' =>
                    'COMPLETADA',
            ]);

            $numeroVenta =
                'VTA-'
                . now()->format('Ymd')
                . '-'
                . str_pad(
                    (string) $venta->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                );

            $venta->update([
                'numero_venta' =>
                    $numeroVenta,
            ]);

            $totalVenta = 0;

            foreach ($datos['productos'] as $item) {
                $producto = Producto::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $item['producto_id']
                    );

                $cantidad =
                    (int) $item['cantidad'];

                if (!$producto->estado) {
                    throw ValidationException::withMessages([
                        'productos' =>
                            "El producto {$producto->nombre} está inactivo.",
                    ]);
                }

                if ($producto->stock < $cantidad) {
                    throw ValidationException::withMessages([
                        'productos' =>
                            "Stock insuficiente para {$producto->nombre}. Stock disponible: {$producto->stock}.",
                    ]);
                }

                $precioUnitario =
                    (float) $producto->precio_venta;

                $subtotal = round(
                    $precioUnitario * $cantidad,
                    2
                );

                $stockAnterior =
                    (int) $producto->stock;

                $stockNuevo =
                    $stockAnterior - $cantidad;

                $venta->detalles()->create([
                    'producto_id' =>
                        $producto->id,

                    'cantidad' =>
                        $cantidad,

                    'precio_unitario' =>
                        $precioUnitario,

                    'subtotal' =>
                        $subtotal,
                ]);

                $producto->update([
                    'stock' =>
                        $stockNuevo,
                ]);

                MovimientoInventario::create([
                    'producto_id' =>
                        $producto->id,

                    'tipo' =>
                        'SALIDA',

                    'cantidad' =>
                        $cantidad,

                    'stock_anterior' =>
                        $stockAnterior,

                    'stock_nuevo' =>
                        $stockNuevo,

                    'motivo' =>
                        'Venta de producto',

                    'referencia' =>
                        $numeroVenta,
                ]);

                $totalVenta += $subtotal;
            }

            $totalVenta = round(
                $totalVenta,
                2
            );

            $venta->update([
                'subtotal' =>
                    $totalVenta,

                'total' =>
                    $totalVenta,
            ]);

            return $venta;
        });

        return redirect()
            ->route('ventas.show', $venta)
            ->with(
                'success',
                'Venta registrada correctamente.'
            );
    }

    /**
     * Muestra el detalle de una venta.
     */
    public function show(Venta $venta): View
    {
        $venta->load([
            'detalles.producto',
            'usuario',
        ]);

        return view(
            'ventas.show',
            compact('venta')
        );
    }
}