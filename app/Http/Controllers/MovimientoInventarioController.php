<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MovimientoInventarioController extends Controller
{
    public function index(Request $request): View
    {
        $movimientos = MovimientoInventario::with(
            'producto.categoria'
        )
            ->when(
                $request->producto_id,
                fn ($consulta, $productoId) =>
                    $consulta->where('producto_id', $productoId)
            )
            ->when(
                $request->tipo,
                fn ($consulta, $tipo) =>
                    $consulta->where('tipo', $tipo)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $productos = Producto::orderBy('nombre')->get();

        return view(
            'movimientos.index',
            compact('movimientos', 'productos')
        );
    }

    public function create(): View
    {
        $productos = Producto::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'movimientos.create',
            compact('productos')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
            ],
            'tipo' => [
                'required',
                Rule::in(['ENTRADA', 'SALIDA', 'AJUSTE']),
            ],
            'cantidad' => [
                'required',
                'integer',
                'min:0',
            ],
            'motivo' => [
                'required',
                'string',
                'max:255',
            ],
            'referencia' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        if (
            in_array($datos['tipo'], ['ENTRADA', 'SALIDA'], true)
            && $datos['cantidad'] < 1
        ) {
            throw ValidationException::withMessages([
                'cantidad' =>
                    'La cantidad debe ser mayor que cero.',
            ]);
        }

        DB::transaction(function () use ($datos) {
            $producto = Producto::query()
                ->lockForUpdate()
                ->findOrFail($datos['producto_id']);

            $stockAnterior = $producto->stock;
            $stockNuevo = $stockAnterior;
            $cantidadMovimiento = $datos['cantidad'];

            if ($datos['tipo'] === 'ENTRADA') {
                $stockNuevo =
                    $stockAnterior + $datos['cantidad'];
            }

            if ($datos['tipo'] === 'SALIDA') {
                if ($datos['cantidad'] > $stockAnterior) {
                    throw ValidationException::withMessages([
                        'cantidad' =>
                            'No existe stock suficiente para realizar la salida.',
                    ]);
                }

                $stockNuevo =
                    $stockAnterior - $datos['cantidad'];
            }

            if ($datos['tipo'] === 'AJUSTE') {
                $stockNuevo = $datos['cantidad'];

                $cantidadMovimiento = abs(
                    $stockNuevo - $stockAnterior
                );
            }

            $producto->update([
                'stock' => $stockNuevo,
            ]);

            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => $datos['tipo'],
                'cantidad' => $cantidadMovimiento,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $datos['motivo'],
                'referencia' => $datos['referencia'] ?? null,
            ]);
        });

        return redirect()
            ->route('movimientos.index')
            ->with(
                'success',
                'Movimiento registrado y stock actualizado correctamente.'
            );
    }
}