<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentaResource;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    /**
     * Lista y filtra las ventas.
     */
    public function index(Request $request): JsonResponse
    {
        $ventas = Venta::with([
            'usuario',
            'detalles.producto',
        ])
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
            ->when(
                $request->fecha_desde,
                function ($consulta, $fechaDesde) {
                    $consulta->whereDate(
                        'fecha',
                        '>=',
                        $fechaDesde
                    );
                }
            )
            ->when(
                $request->fecha_hasta,
                function ($consulta, $fechaHasta) {
                    $consulta->whereDate(
                        'fecha',
                        '<=',
                        $fechaHasta
                    );
                }
            )
            ->when(
                $request->estado,
                function ($consulta, $estado) {
                    $consulta->where(
                        'estado',
                        strtoupper($estado)
                    );
                }
            )
            ->latest('fecha')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Ventas consultadas correctamente.',
            'data' => VentaResource::collection(
                $ventas->items()
            ),
            'pagination' => [
                'pagina_actual' => $ventas->currentPage(),
                'ultima_pagina' => $ventas->lastPage(),
                'por_pagina' => $ventas->perPage(),
                'total' => $ventas->total(),
            ],
        ]);
    }

    /**
     * Registra una venta.
     */
    public function store(Request $request): JsonResponse
    {
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

            'metodo_pago' => [
                'required',
                'string',
                'in:EFECTIVO,YAPE,PLIN',
            ],

            'monto_recibido' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $venta = DB::transaction(
            function () use ($datos) {

                $metodoPago = strtoupper(
                    $datos['metodo_pago']
                );

                $montoRecibido = round(
                    (float) $datos['monto_recibido'],
                    2
                );

                $venta = Venta::create([
                    'user_id' => auth()->id(),

                    'numero_venta' =>
                        'TMP-' . Str::upper(
                            Str::random(20)
                        ),

                    'fecha' => now(),

                    'subtotal' => 0,

                    'total' => 0,

                    'metodo_pago' => $metodoPago,

                    'monto_recibido' => $montoRecibido,

                    'vuelto' => 0,

                    'estado' => 'COMPLETADA',
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
                    'numero_venta' => $numeroVenta,
                ]);

                $totalVenta = 0;

                foreach ($datos['productos'] as $item) {

                    $producto = Producto::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $item['producto_id']
                        );

                    $cantidad = (int) $item['cantidad'];

                    if (!$producto->estado) {
                        throw ValidationException::withMessages([
                            'productos' =>
                                "El producto {$producto->nombre} está inactivo.",
                        ]);
                    }

                    if ($producto->stock < $cantidad) {
                        throw ValidationException::withMessages([
                            'productos' =>
                                "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}.",
                        ]);
                    }

                    $precioCompra = round(
                        (float) $producto->precio_compra,
                        2
                    );

                    $precioVenta = round(
                        (float) $producto->precio_venta,
                        2
                    );

                    $subtotal = round(
                        $precioVenta * $cantidad,
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

                        'precio_compra_unitario' =>
                            $precioCompra,

                        'precio_unitario' =>
                            $precioVenta,

                        'subtotal' =>
                            $subtotal,
                    ]);

                    $producto->update([
                        'stock' => $stockNuevo,
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

                /*
                 * Para YAPE y PLIN el monto recibido
                 * debe ser exactamente el total.
                 */
                if (
                    $metodoPago !== 'EFECTIVO'
                    && $montoRecibido != $totalVenta
                ) {
                    throw ValidationException::withMessages([
                        'monto_recibido' =>
                            'Para YAPE o PLIN, el monto recibido debe ser igual al total.',
                    ]);
                }

                /*
                 * En efectivo no se puede recibir
                 * menos dinero que el total.
                 */
                if (
                    $metodoPago === 'EFECTIVO'
                    && $montoRecibido < $totalVenta
                ) {
                    throw ValidationException::withMessages([
                        'monto_recibido' =>
                            "El monto recibido es insuficiente. Total: S/ {$totalVenta}.",
                    ]);
                }

                $vuelto = 0;

                if ($metodoPago === 'EFECTIVO') {
                    $vuelto = round(
                        $montoRecibido - $totalVenta,
                        2
                    );
                }

                $venta->update([
                    'subtotal' =>
                        $totalVenta,

                    'total' =>
                        $totalVenta,

                    'monto_recibido' =>
                        $montoRecibido,

                    'vuelto' =>
                        $vuelto,
                ]);

                return $venta;
            }
        );

        $venta->load([
            'usuario',
            'detalles.producto',
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Venta registrada correctamente.',

            'data' =>
                new VentaResource($venta),

        ], 201);
    }

    /**
     * Consulta una venta específica.
     */
    public function show(Venta $venta): JsonResponse
    {
        $venta->load([
            'usuario',
            'detalles.producto',
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Venta consultada correctamente.',

            'data' =>
                new VentaResource($venta),
        ]);
    }
}