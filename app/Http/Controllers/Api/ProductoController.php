<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Lista y filtra los productos.
     */
    public function index(Request $request): JsonResponse
    {
        $productos = Producto::with('categoria')
            ->when(
                $request->buscar,
                function ($consulta, $buscar) {
                    $consulta->where(
                        function ($subconsulta) use ($buscar) {
                            $subconsulta
                                ->where(
                                    'nombre',
                                    'like',
                                    "%{$buscar}%"
                                )
                                ->orWhere(
                                    'codigo_barras',
                                    'like',
                                    "%{$buscar}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->categoria_id,
                function ($consulta, $categoriaId) {
                    $consulta->where(
                        'categoria_id',
                        $categoriaId
                    );
                }
            )
            ->when(
                $request->filled('estado'),
                function ($consulta) use ($request) {
                    $consulta->where(
                        'estado',
                        $request->boolean('estado')
                    );
                }
            )
            ->when(
                $request->boolean('stock_bajo'),
                function ($consulta) {
                    $consulta->whereColumn(
                        'stock',
                        '<=',
                        'stock_minimo'
                    );
                }
            )
            ->orderBy('nombre')
            ->paginate(10);

        return response()->json([
            'success' => true,

            'message' =>
                'Productos consultados correctamente.',

            'data' => ProductoResource::collection(
                $productos->items()
            ),

            'pagination' => [
                'pagina_actual' =>
                    $productos->currentPage(),

                'ultima_pagina' =>
                    $productos->lastPage(),

                'por_pagina' =>
                    $productos->perPage(),

                'total' =>
                    $productos->total(),
            ],
        ]);
    }

    /**
     * Registra un producto.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],

            'codigo_barras' => [
                'required',
                'string',
                'max:50',
                'unique:productos,codigo_barras',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],

            'precio_compra' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'precio_venta' => [
                'required',
                'numeric',
                'gte:precio_compra',
                'decimal:0,2',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'stock_minimo' => [
                'required',
                'integer',
                'min:0',
            ],

            'estado' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $producto = DB::transaction(
            function () use ($datos) {
                $stockInicial = (int) $datos['stock'];

                $datos['estado'] =
                    $datos['estado'] ?? true;

                $producto = Producto::create($datos);

                if ($stockInicial > 0) {
                    MovimientoInventario::create([
                        'producto_id' => $producto->id,
                        'tipo' => 'ENTRADA',
                        'cantidad' => $stockInicial,
                        'stock_anterior' => 0,
                        'stock_nuevo' => $stockInicial,
                        'motivo' => 'Stock inicial del producto',
                        'referencia' => 'REGISTRO-INICIAL',
                    ]);
                }

                return $producto;
            }
        );

        $producto->load('categoria');

        return response()->json([
            'success' => true,

            'message' =>
                'Producto registrado correctamente.',

            'data' => new ProductoResource($producto),
        ], 201);
    }

    /**
     * Consulta un producto.
     */
    public function show(
        Producto $producto
    ): JsonResponse {
        $producto->load('categoria');

        return response()->json([
            'success' => true,

            'message' =>
                'Producto consultado correctamente.',

            'data' => new ProductoResource($producto),
        ]);
    }

    /**
     * Actualiza un producto.
     */
    public function update(
        Request $request,
        Producto $producto
    ): JsonResponse {
        $datos = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],

            'codigo_barras' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'productos',
                    'codigo_barras'
                )->ignore($producto->id),
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],

            'precio_compra' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'precio_venta' => [
                'required',
                'numeric',
                'gte:precio_compra',
                'decimal:0,2',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'stock_minimo' => [
                'required',
                'integer',
                'min:0',
            ],

            'estado' => [
                'required',
                'boolean',
            ],
        ]);

        $producto->update($datos);

        $producto->load('categoria');

        return response()->json([
            'success' => true,

            'message' =>
                'Producto actualizado correctamente.',

            'data' => new ProductoResource(
                $producto->fresh(['categoria'])
            ),
        ]);
    }

    /**
     * Elimina un producto sin historial.
     */
    public function destroy(
        Producto $producto
    ): JsonResponse {
        $tieneMovimientos =
            $producto->movimientosInventario()->exists();

        $tieneVentas = DetalleVenta::where(
            'producto_id',
            $producto->id
        )->exists();

        if ($tieneMovimientos || $tieneVentas) {
            return response()->json([
                'success' => false,

                'message' =>
                    'No se puede eliminar el producto porque tiene movimientos o ventas registradas. Debes cambiar su estado a inactivo.',
            ], 409);
        }

        $producto->delete();

        return response()->json([
            'success' => true,

            'message' =>
                'Producto eliminado correctamente.',

            'data' => null,
        ]);
    }
}