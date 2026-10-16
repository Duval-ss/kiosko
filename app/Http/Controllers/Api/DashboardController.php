<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function resumen(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'fecha' => ['sometimes', 'date_format:Y-m-d'],
        ]);

        $fecha = $datos['fecha'] ?? now()->toDateString();

        $ventas = Venta::with('detalles')
            ->whereDate('fecha', $fecha)
            ->where('estado', 'COMPLETADA')
            ->get();

        $totalVendido = 0;
        $costoProductos = 0;
        $efectivo = 0;
        $yape = 0;
        $plin = 0;
        $productosVendidos = 0;

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

                // Usar el costo guardado en la venta.
                $costo = $detalle->precio_compra_unitario;

                if ($costo !== null) {
                    $costoProductos += (float) $costo * $cantidad;
                }
            }
        }

        $gastoCompras = Compra::whereDate('fecha', $fecha)
            ->where('estado', 'REGISTRADA')
            ->sum('total');

        $productosMasVendidos = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->whereDate('ventas.fecha', $fecha)
            ->where('ventas.estado', 'COMPLETADA')
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as unidades_vendidas'),
                DB::raw('SUM(detalle_ventas.subtotal) as importe_vendido')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('unidades_vendidas')
            ->limit(5)
            ->get();

        $totalVendido = round($totalVendido, 2);
        $costoProductos = round($costoProductos, 2);

        return response()->json([
            'success' => true,
            'data' => [
                'fecha' => $fecha,
                'ventas_realizadas' => $ventas->count(),
                'productos_vendidos' => $productosVendidos,
                'total_vendido' => round($totalVendido, 2),
                'costo_productos' => round($costoProductos, 2),
                'utilidad' => round($totalVendido - $costoProductos, 2),
                'gasto_compras' => round((float) $gastoCompras, 2),
                'efectivo' => round($efectivo, 2),
                'yape' => round($yape, 2),
                'plin' => round($plin, 2),
                'productos_mas_vendidos' => $productosMasVendidos,
            ],
        ]);
    }
}
