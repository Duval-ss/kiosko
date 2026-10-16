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
use Illuminate\Validation\ValidationException;

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
                    $consulta->where(function ($subconsulta) use ($buscar) {
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
                    });
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
     * Registra un producto o comida.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'tipo' => [
                'required',
                Rule::in([
                    'PRODUCTO',
                    'COMIDA',
                ]),
            ],

            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],

            'codigo_barras' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
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

            /*
            |--------------------------------------------------------------------------
            | Compra
            |--------------------------------------------------------------------------
            */

            'precio_compra' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'unidad_compra' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
                'string',
                'max:50',
            ],

            'cantidad_por_paquete' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
                'integer',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | Venta
            |--------------------------------------------------------------------------
            */

            'precio_venta' => [
                'required',
                'numeric',
                'min:0',
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

        /*
        |--------------------------------------------------------------------------
        | PRODUCTO
        |--------------------------------------------------------------------------
        |
        | Ejemplo:
        |
        | Precio paquete = S/30
        | 12 unidades
        |
        | Costo unitario = 30 / 12 = S/2.50
        |
        */

        if ($datos['tipo'] === 'PRODUCTO') {

            $cantidadPorPaquete =
                (int) $datos['cantidad_por_paquete'];

            $datos['costo_unitario'] = round(
                $datos['precio_compra'] / $cantidadPorPaquete,
                2
            );

            /*
            |----------------------------------------------------------------------
            | El precio de venta es por unidad.
            |----------------------------------------------------------------------
            */

            if (
                $datos['precio_venta'] <
                $datos['costo_unitario']
            ) {
                throw ValidationException::withMessages([
                    'precio_venta' =>
                        'El precio de venta no puede ser menor que el costo por unidad.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | COMIDA
        |--------------------------------------------------------------------------
        |
        | Las comidas se venden por unidad.
        | No utilizan código de barras ni compra por paquete.
        |
        */

        if ($datos['tipo'] === 'COMIDA') {

            $datos['codigo_barras'] = null;

            $datos['precio_compra'] = 0;

            $datos['unidad_compra'] = 'UNIDAD';

            $datos['cantidad_por_paquete'] = 1;

            $datos['costo_unitario'] = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Crear producto + movimiento inicial
        |--------------------------------------------------------------------------
        */

        $producto = DB::transaction(
            function () use ($datos) {

                $stockInicial =
                    (int) $datos['stock'];

                $datos['estado'] =
                    $datos['estado'] ?? true;

                $producto =
                    Producto::create($datos);

                /*
                |--------------------------------------------------------------------------
                | Movimiento de stock inicial
                |--------------------------------------------------------------------------
                */

                if ($stockInicial > 0) {

                    MovimientoInventario::create([
                        'producto_id' =>
                            $producto->id,

                        'tipo' =>
                            'ENTRADA',

                        'cantidad' =>
                            $stockInicial,

                        'stock_anterior' =>
                            0,

                        'stock_nuevo' =>
                            $stockInicial,

                        'motivo' =>
                            $datos['tipo'] === 'COMIDA'
                                ? 'Stock inicial de comida'
                                : 'Stock inicial del producto',

                        'referencia' =>
                            'REGISTRO-INICIAL',
                    ]);
                }

                return $producto;
            }
        );

        $producto->load('categoria');

        return response()->json([
            'success' => true,

            'message' =>
                $datos['tipo'] === 'COMIDA'
                    ? 'Comida registrada correctamente.'
                    : 'Producto registrado correctamente.',

            'data' =>
                new ProductoResource($producto),

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

            'data' =>
                new ProductoResource($producto),
        ]);
    }

    /**
     * Actualiza un producto o comida.
     */
    public function update(
        Request $request,
        Producto $producto
    ): JsonResponse {

        $datos = $request->validate([
            'tipo' => [
                'required',
                Rule::in([
                    'PRODUCTO',
                    'COMIDA',
                ]),
            ],

            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],

            'codigo_barras' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
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

            /*
            |--------------------------------------------------------------------------
            | Compra
            |--------------------------------------------------------------------------
            */

            'precio_compra' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'unidad_compra' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
                'string',
                'max:50',
            ],

            'cantidad_por_paquete' => [
                'required_if:tipo,PRODUCTO',
                'nullable',
                'integer',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | Venta
            |--------------------------------------------------------------------------
            */

            'precio_venta' => [
                'required',
                'numeric',
                'min:0',
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

        /*
        |--------------------------------------------------------------------------
        | PRODUCTO
        |--------------------------------------------------------------------------
        */

        if ($datos['tipo'] === 'PRODUCTO') {

            $cantidadPorPaquete =
                (int) $datos['cantidad_por_paquete'];

            $datos['costo_unitario'] = round(
                $datos['precio_compra'] / $cantidadPorPaquete,
                2
            );

            if (
                $datos['precio_venta'] <
                $datos['costo_unitario']
            ) {
                throw ValidationException::withMessages([
                    'precio_venta' =>
                        'El precio de venta no puede ser menor que el costo por unidad.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | COMIDA
        |--------------------------------------------------------------------------
        */

        if ($datos['tipo'] === 'COMIDA') {

            $datos['codigo_barras'] = null;

            $datos['precio_compra'] = 0;

            $datos['unidad_compra'] = 'UNIDAD';

            $datos['cantidad_por_paquete'] = 1;

            $datos['costo_unitario'] = 0;
        }

        $producto->update($datos);

        $producto->load('categoria');

        return response()->json([
            'success' => true,

            'message' =>
                $datos['tipo'] === 'COMIDA'
                    ? 'Comida actualizada correctamente.'
                    : 'Producto actualizado correctamente.',

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
            $producto
                ->movimientosInventario()
                ->exists();

        $tieneVentas =
            DetalleVenta::where(
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