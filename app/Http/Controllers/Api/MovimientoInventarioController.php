<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovimientoInventarioResource;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MovimientoInventarioController extends Controller
{
    /**
     * Lista y filtra los movimientos.
     */
    public function index(Request $request): JsonResponse
    {
        $movimientos = MovimientoInventario::with(
            'producto'
        )
            ->when(
                $request->producto_id,
                function ($consulta, $productoId) {
                    $consulta->where(
                        'producto_id',
                        $productoId
                    );
                }
            )
            ->when(
                $request->tipo,
                function ($consulta, $tipo) {
                    $consulta->where(
                        'tipo',
                        strtoupper($tipo)
                    );
                }
            )
            ->when(
                $request->fecha_desde,
                function ($consulta, $fechaDesde) {
                    $consulta->whereDate(
                        'created_at',
                        '>=',
                        $fechaDesde
                    );
                }
            )
            ->when(
                $request->fecha_hasta,
                function ($consulta, $fechaHasta) {
                    $consulta->whereDate(
                        'created_at',
                        '<=',
                        $fechaHasta
                    );
                }
            )
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,

            'message' =>
                'Movimientos consultados correctamente.',

            'data' =>
                MovimientoInventarioResource::collection(
                    $movimientos->items()
                ),

            'pagination' => [
                'pagina_actual' =>
                    $movimientos->currentPage(),

                'ultima_pagina' =>
                    $movimientos->lastPage(),

                'por_pagina' =>
                    $movimientos->perPage(),

                'total' =>
                    $movimientos->total(),
            ],
        ]);
    }

    /**
     * Registra una entrada o salida de inventario.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
            ],

            'tipo' => [
                'required',
                'string',

                Rule::in([
                    'ENTRADA',
                    'SALIDA',
                ]),
            ],

            'cantidad' => [
                'required',
                'integer',
                'min:1',
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

        $movimiento = DB::transaction(
            function () use ($datos) {
                $producto = Producto::query()
                    ->lockForUpdate()
                    ->findOrFail($datos['producto_id']);

                $stockAnterior = (int) $producto->stock;
                $cantidad = (int) $datos['cantidad'];

                if ($datos['tipo'] === 'ENTRADA') {
                    $stockNuevo =
                        $stockAnterior + $cantidad;
                } else {
                    if ($cantidad > $stockAnterior) {
                        throw ValidationException::withMessages([
                            'cantidad' =>
                                "Stock insuficiente. El producto solamente tiene {$stockAnterior} unidades disponibles.",
                        ]);
                    }

                    $stockNuevo =
                        $stockAnterior - $cantidad;
                }

                $producto->update([
                    'stock' => $stockNuevo,
                ]);

                return MovimientoInventario::create([
                    'producto_id' =>
                        $producto->id,

                    'tipo' =>
                        $datos['tipo'],

                    'cantidad' =>
                        $cantidad,

                    'stock_anterior' =>
                        $stockAnterior,

                    'stock_nuevo' =>
                        $stockNuevo,

                    'motivo' =>
                        $datos['motivo'],

                    'referencia' =>
                        $datos['referencia'] ?? null,
                ]);
            }
        );

        $movimiento->load('producto');

        return response()->json([
            'success' => true,

            'message' =>
                'Movimiento registrado correctamente.',

            'data' =>
                new MovimientoInventarioResource(
                    $movimiento
                ),
        ], 201);
    }

    /**
     * Consulta un movimiento específico.
     */
    public function show(
        MovimientoInventario $movimiento
    ): JsonResponse {
        $movimiento->load('producto');

        return response()->json([
            'success' => true,

            'message' =>
                'Movimiento consultado correctamente.',

            'data' =>
                new MovimientoInventarioResource(
                    $movimiento
                ),
        ]);
    }
}