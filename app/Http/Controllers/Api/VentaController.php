<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentaResource;
use App\Models\Caja;
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
        $datos = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'fecha_desde' => ['nullable', 'date_format:Y-m-d'],
            'fecha_hasta' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_desde',
            ],
            'estado' => ['nullable', 'string', 'max:30'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $ventas = Venta::with([
            'usuario',
            'caja',
            'detalles.producto',
        ])
            ->when(
                $datos['buscar'] ?? null,
                fn ($consulta, $buscar) =>
                    $consulta->where(
                        'numero_venta',
                        'like',
                        "%{$buscar}%"
                    )
            )
            ->when(
                $datos['fecha_desde'] ?? null,
                fn ($consulta, $fechaDesde) =>
                    $consulta->whereDate('fecha', '>=', $fechaDesde)
            )
            ->when(
                $datos['fecha_hasta'] ?? null,
                fn ($consulta, $fechaHasta) =>
                    $consulta->whereDate('fecha', '<=', $fechaHasta)
            )
            ->when(
                $datos['estado'] ?? null,
                fn ($consulta, $estado) =>
                    $consulta->where('estado', strtoupper($estado))
            )
            ->latest('fecha')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Ventas consultadas correctamente.',
            'data' => VentaResource::collection($ventas->items()),
            'pagination' => [
                'pagina_actual' => $ventas->currentPage(),
                'ultima_pagina' => $ventas->lastPage(),
                'por_pagina' => $ventas->perPage(),
                'total' => $ventas->total(),
            ],
        ]);
    }

    /**
     * Resumen de ventas de una fecha.
     */
    public function resumen(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'fecha' => ['sometimes', 'date_format:Y-m-d'],
        ]);

        $fecha = $datos['fecha'] ?? now()->toDateString();

        $ventas = Venta::with('detalles.producto')
            ->whereDate('fecha', $fecha)
            ->where('estado', 'COMPLETADA')
            ->get();

        $totalVendido = 0.0;
        $costoProductos = 0.0;
        $productosVendidos = 0;
        $efectivo = 0.0;
        $yape = 0.0;
        $plin = 0.0;

        foreach ($ventas as $venta) {
            $total = (float) $venta->total;

            $totalVendido += $total;

            switch (strtoupper((string) $venta->metodo_pago)) {
                case 'EFECTIVO':
                    $efectivo += $total;
                    break;
                case 'YAPE':
                    $yape += $total;
                    break;
                case 'PLIN':
                    $plin += $total;
                    break;
            }

            foreach ($venta->detalles as $detalle) {
                $cantidad = (int) $detalle->cantidad;
                $productosVendidos += $cantidad;

                $costoUnitario = $detalle->precio_compra_unitario;

                if ($costoUnitario !== null) {
                    $costoUnitario = (float) $costoUnitario;
                } elseif ($detalle->producto) {
                    $costoUnitario = $this->obtenerCostoUnitario(
                        $detalle->producto
                    );
                } else {
                    $costoUnitario = 0;
                }

                $costoProductos += $costoUnitario * $cantidad;
            }
        }

        $totalVendido = round($totalVendido, 2);
        $costoProductos = round($costoProductos, 2);
        $utilidad = round($totalVendido - $costoProductos, 2);

        return response()->json([
            'success' => true,
            'message' => 'Resumen de ventas consultado correctamente.',
            'data' => [
                'fecha' => $fecha,
                'ventas_realizadas' => $ventas->count(),
                'productos_vendidos' => $productosVendidos,
                'efectivo' => number_format($efectivo, 2, '.', ''),
                'yape' => number_format($yape, 2, '.', ''),
                'plin' => number_format($plin, 2, '.', ''),
                'total_vendido' => number_format(
                    $totalVendido,
                    2,
                    '.',
                    ''
                ),
                'costo_productos' => number_format(
                    $costoProductos,
                    2,
                    '.',
                    ''
                ),
                'utilidad' => number_format(
                    $utilidad,
                    2,
                    '.',
                    ''
                ),
            ],
        ]);
    }

    /**
     * Registra una venta dentro de la caja abierta.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'productos' => [
                'required',
                'array',
                'min:1',
                'max:100',
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

        $venta = DB::transaction(function () use ($datos) {
            /*
             * 1. Buscar y bloquear la caja abierta.
             */
            $caja = Caja::where('estado', 'ABIERTA')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$caja) {
                throw ValidationException::withMessages([
                    'caja' => [
                        'No existe una caja abierta. Debe abrir la caja antes de registrar ventas.',
                    ],
                ]);
            }

            $metodoPago = strtoupper($datos['metodo_pago']);

            $montoRecibido = round(
                (float) $datos['monto_recibido'],
                2
            );

            /*
             * 2. Crear venta vinculada a la caja.
             */
            $venta = Venta::create([
                'user_id' => auth()->id(),
                'caja_id' => $caja->id,
                'numero_venta' => 'TMP-' . Str::upper(
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

            /*
             * 3. Generar número definitivo.
             */
            $numeroVenta = 'VTA-'
                . $venta->fecha->format('Ymd')
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

            /*
             * 4. Validar productos y calcular importes.
             */
            $totalVenta = 0.0;

            foreach ($datos['productos'] as $item) {
                $producto = Producto::query()
                    ->lockForUpdate()
                    ->findOrFail($item['producto_id']);

                $cantidad = (int) $item['cantidad'];

                if (!$producto->estado) {
                    throw ValidationException::withMessages([
                        'productos' => [
                            "El producto {$producto->nombre} está inactivo.",
                        ],
                    ]);
                }

                if ((int) $producto->stock < $cantidad) {
                    throw ValidationException::withMessages([
                        'productos' => [
                            "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}.",
                        ],
                    ]);
                }

                $precioCompra = $this->obtenerCostoUnitario($producto);

                $precioVenta = round(
                    (float) $producto->precio_venta,
                    2
                );

                if ($precioVenta <= 0) {
                    throw ValidationException::withMessages([
                        'productos' => [
                            "El precio del producto {$producto->nombre} debe ser mayor que cero.",
                        ],
                    ]);
                }

                $subtotal = round($precioVenta * $cantidad, 2);

                $stockAnterior = (int) $producto->stock;
                $stockNuevo = $stockAnterior - $cantidad;

                /*
                 * 5. Guardar detalle con costo histórico.
                 */
                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_compra_unitario' => $precioCompra,
                    'precio_unitario' => $precioVenta,
                    'subtotal' => $subtotal,
                ]);

                /*
                 * 6. Descontar stock.
                 */
                $producto->update([
                    'stock' => $stockNuevo,
                ]);

                /*
                 * 7. Registrar movimiento de inventario.
                 */
                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'SALIDA',
                    'cantidad' => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => 'Venta de producto',
                    'referencia' => $numeroVenta,
                ]);

                $totalVenta += $subtotal;
            }

            $totalVenta = round($totalVenta, 2);

            /*
             * 8. Validar pago y calcular vuelto.
             */
            if (
                $metodoPago !== 'EFECTIVO'
                && abs($montoRecibido - $totalVenta) > 0.001
            ) {
                throw ValidationException::withMessages([
                    'monto_recibido' => [
                        'Para YAPE o PLIN, el monto recibido debe ser igual al total.',
                    ],
                ]);
            }

            if (
                $metodoPago === 'EFECTIVO'
                && $montoRecibido < $totalVenta
            ) {
                throw ValidationException::withMessages([
                    'monto_recibido' => [
                        "El monto recibido es insuficiente. Total: S/ {$totalVenta}.",
                    ],
                ]);
            }

            $vuelto = $metodoPago === 'EFECTIVO'
                ? round($montoRecibido - $totalVenta, 2)
                : 0;

            $venta->update([
                'subtotal' => $totalVenta,
                'total' => $totalVenta,
                'monto_recibido' => $montoRecibido,
                'vuelto' => $vuelto,
            ]);

            /*
             * 9. Actualizar total acumulado de caja.
             */
            $caja->increment('total_ventas', $totalVenta);

            return $venta;
        }, 3);

        $venta->load([
            'usuario',
            'caja',
            'detalles.producto',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Venta registrada correctamente.',
            'data' => new VentaResource($venta),
        ], 201);
    }

    /**
     * Consulta una venta específica.
     */
    public function show(Venta $venta): JsonResponse
    {
        $venta->load([
            'usuario',
            'caja',
            'detalles.producto',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Venta consultada correctamente.',
            'data' => new VentaResource($venta),
        ]);
    }

    /**
     * Calcula el costo unitario del producto.
     */
    private function obtenerCostoUnitario(Producto $producto): float
    {
        $costoUnitario = (float) (
            $producto->costo_unitario ?? 0
        );

        if ($costoUnitario > 0) {
            return round($costoUnitario, 4);
        }

        $precioCompra = (float) (
            $producto->precio_compra ?? 0
        );

        $cantidadPorPaquete = (float) (
            $producto->cantidad_por_paquete ?? 0
        );

        $unidadCompra = strtoupper(
            (string) ($producto->unidad_compra ?? 'UNIDAD')
        );

        if (
            $unidadCompra === 'PAQUETE'
            && $cantidadPorPaquete > 0
            && $precioCompra > 0
        ) {
            return round(
                $precioCompra / $cantidadPorPaquete,
                4
            );
        }

        return round($precioCompra, 4);
    }
}
