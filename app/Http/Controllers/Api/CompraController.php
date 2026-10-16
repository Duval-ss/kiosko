<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    /**
     * Listar compras
     */
    public function index()
    {
        $compras = Compra::with([
            'usuario:id,name,email',
            'detalles.producto:id,nombre,codigo_barras'
        ])
        ->orderByDesc('fecha')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $compras,
        ]);
    }

    /**
     * Registrar una compra
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'proveedor' => [
                'nullable',
                'string',
                'max:150',
            ],

            'fecha' => [
                'nullable',
                'date',
            ],

            'productos' => [
                'required',
                'array',
                'min:1',
            ],

            'productos.*.producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
            ],

            'productos.*.cantidad_paquetes' => [
                'required',
                'integer',
                'min:1',
            ],

            'productos.*.precio_paquete' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $compra = DB::transaction(function () use ($datos, $request) {

            $total = 0;

            /*
             * Primero calculamos el total.
             */
            foreach ($datos['productos'] as $item) {

                $producto = Producto::findOrFail(
                    $item['producto_id']
                );

                if ($producto->tipo !== 'PRODUCTO') {
                    throw ValidationException::withMessages([
                        'productos' =>
                            "El artículo {$producto->nombre} no es un producto comercial."
                    ]);
                }

                $cantidadPaquetes =
                    (int) $item['cantidad_paquetes'];

                $unidadesPorPaquete =
                    (int) $producto->cantidad_por_paquete;

                if ($unidadesPorPaquete < 1) {
                    throw ValidationException::withMessages([
                        'productos' =>
                            "El producto {$producto->nombre} no tiene configurada la cantidad por paquete."
                    ]);
                }

                $precioPaquete =
                    (float) $item['precio_paquete'];

                $total +=
                    $cantidadPaquetes * $precioPaquete;
            }

            /*
             * Generar número de compra.
             */
            $numeroCompra = 'COM-' .
                now()->format('Ymd') . '-' .
                str_pad(
                    (string) (
                        Compra::whereDate(
                            'fecha',
                            now()->toDateString()
                        )->count() + 1
                    ),
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            /*
             * Crear cabecera de compra.
             */
            $compra = Compra::create([
                'user_id' => $request->user()->id,
                'numero_compra' => $numeroCompra,
                'proveedor' => $datos['proveedor'] ?? null,
                'fecha' => $datos['fecha'] ?? now(),
                'total' => $total,
                'estado' => 'REGISTRADA',
            ]);

            /*
             * Registrar cada producto comprado.
             */
            foreach ($datos['productos'] as $item) {

                $producto = Producto::lockForUpdate()
                    ->findOrFail($item['producto_id']);

                $cantidadPaquetes =
                    (int) $item['cantidad_paquetes'];

                $unidadesPorPaquete =
                    (int) $producto->cantidad_por_paquete;

                $unidadesIngresadas =
                    $cantidadPaquetes *
                    $unidadesPorPaquete;

                $precioPaquete =
                    (float) $item['precio_paquete'];

                $subtotal =
                    $cantidadPaquetes *
                    $precioPaquete;

                /*
                 * Stock antes de la compra.
                 */
                $stockAnterior = $producto->stock;

                /*
                 * Nuevo stock.
                 */
                $stockNuevo =
                    $stockAnterior +
                    $unidadesIngresadas;

                /*
                 * Actualizar costo del producto.
                 */
                $producto->precio_compra =
                    $precioPaquete;

                $producto->costo_unitario =
                    round(
                        $precioPaquete /
                        $unidadesPorPaquete,
                        2
                    );

                /*
                 * Actualizar stock.
                 */
                $producto->stock = $stockNuevo;

                $producto->save();

                /*
                 * Guardar detalle de compra.
                 */
                DetalleCompra::create([
                    'compra_id' =>
                        $compra->id,

                    'producto_id' =>
                        $producto->id,

                    'cantidad_paquetes' =>
                        $cantidadPaquetes,

                    'unidades_por_paquete' =>
                        $unidadesPorPaquete,

                    'unidades_ingresadas' =>
                        $unidadesIngresadas,

                    'precio_paquete' =>
                        $precioPaquete,

                    'subtotal' =>
                        $subtotal,
                ]);

                /*
                 * Registrar movimiento de inventario.
                 */
                MovimientoInventario::create([
                    'producto_id' =>
                        $producto->id,

                    'tipo' =>
                        'ENTRADA',

                    'cantidad' =>
                        $unidadesIngresadas,

                    'stock_anterior' =>
                        $stockAnterior,

                    'stock_nuevo' =>
                        $stockNuevo,

                    'motivo' =>
                        'COMPRA',

                    'referencia' =>
                        $compra->numero_compra,
                ]);
            }

            return $compra;
        });

        /*
         * Cargar información relacionada.
         */
        $compra->load([
            'usuario:id,name,email',
            'detalles.producto'
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Compra registrada correctamente.',
            'data' => $compra,
        ], 201);
    }

    /**
     * Mostrar una compra.
     */
    public function show($id)
    {
        $compra = Compra::with([
            'usuario:id,name,email',
            'detalles.producto'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $compra,
        ]);
    }
}