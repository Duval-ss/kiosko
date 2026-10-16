<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    /**
     * Consultar la caja abierta y sus totales.
     */
    public function actual(): JsonResponse
    {
        $caja = Caja::where('estado', 'ABIERTA')
            ->latest('id')
            ->first();

        if (!$caja) {
            return response()->json([
                'success' => true,
                'message' => 'No hay una caja abierta.',
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Caja consultada correctamente.',
            'data' => array_merge(
                $caja->toArray(),
                $this->calcularResumen($caja)
            ),
        ]);
    }

    /**
     * Abrir una nueva caja.
     */
    public function abrir(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'monto_inicial' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ]);

        $caja = DB::transaction(function () use ($datos) {
            $cajaAbierta = Caja::where('estado', 'ABIERTA')
                ->lockForUpdate()
                ->first();

            if ($cajaAbierta) {
                return null;
            }

            return Caja::create([
                'usuario_id' => Auth::id(),
                'monto_inicial' => $datos['monto_inicial'],
                'total_ventas' => 0,
                'monto_cierre' => null,
                'fecha_apertura' => now(),
                'fecha_cierre' => null,
                'estado' => 'ABIERTA',
            ]);
        }, 3);

        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una caja abierta.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Caja abierta correctamente.',
            'data' => array_merge(
                $caja->toArray(),
                $this->calcularResumen($caja)
            ),
        ], 201);
    }

    /**
     * Cerrar caja y calcular la diferencia de efectivo.
     *
     * monto_cierre representa el efectivo contado físicamente.
     */
    public function cerrar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'monto_cierre' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ]);

        $resultado = DB::transaction(function () use ($datos) {
            $caja = Caja::where('estado', 'ABIERTA')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$caja) {
                return null;
            }

            // Obtener los totales definitivos antes del cierre.
            $resumen = $this->calcularResumen($caja);

            $montoContado = round(
                (float) $datos['monto_cierre'],
                2
            );

            $efectivoEsperado = (float) $resumen[
                'efectivo_esperado'
            ];

            $diferencia = round(
                $montoContado - $efectivoEsperado,
                2
            );

            $caja->update([
                'monto_cierre' => $montoContado,
                'fecha_cierre' => now(),
                'estado' => 'CERRADA',
            ]);

            return [
                'caja' => $caja->fresh(),
                'resumen' => array_merge($resumen, [
                    'monto_cierre' => number_format(
                        $montoContado,
                        2,
                        '.',
                        ''
                    ),
                    'efectivo_esperado' => number_format(
                        $efectivoEsperado,
                        2,
                        '.',
                        ''
                    ),
                    'diferencia_efectivo' => number_format(
                        $diferencia,
                        2,
                        '.',
                        ''
                    ),
                    'resultado_caja' => $diferencia > 0
                        ? 'SOBRANTE'
                        : ($diferencia < 0 ? 'FALTANTE' : 'CUADRADA'),
                ]),
            ];
        }, 3);

        if (!$resultado) {
            return response()->json([
                'success' => false,
                'message' => 'No existe una caja abierta.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Caja cerrada correctamente.',
            'data' => array_merge(
                $resultado['caja']->toArray(),
                $resultado['resumen']
            ),
        ]);
    }

    /**
     * Consultar resumen de la caja abierta.
     */
    public function resumen(): JsonResponse
    {
        $caja = Caja::where('estado', 'ABIERTA')
            ->latest('id')
            ->first();

        if (!$caja) {
            return response()->json([
                'success' => true,
                'message' => 'No hay una caja abierta.',
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resumen de caja consultado correctamente.',
            'data' => array_merge(
                $caja->toArray(),
                $this->calcularResumen($caja)
            ),
        ]);
    }

    /**
     * Calcular ventas por medio de pago y efectivo esperado.
     */
    private function calcularResumen(Caja $caja): array
    {
        $ventas = Venta::query()
            ->where('caja_id', $caja->id)
            ->where('estado', 'COMPLETADA')
            ->get([
                'total',
                'metodo_pago',
            ]);

        $efectivo = 0.0;
        $yape = 0.0;
        $plin = 0.0;

        foreach ($ventas as $venta) {
            $total = (float) $venta->total;

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
        }

        $efectivo = round($efectivo, 2);
        $yape = round($yape, 2);
        $plin = round($plin, 2);

        $totalVendido = round(
            $efectivo + $yape + $plin,
            2
        );

        $efectivoEsperado = round(
            (float) $caja->monto_inicial + $efectivo,
            2
        );

        return [
            'ventas_realizadas' => $ventas->count(),
            'total_efectivo' => number_format(
                $efectivo,
                2,
                '.',
                ''
            ),
            'total_yape' => number_format(
                $yape,
                2,
                '.',
                ''
            ),
            'total_plin' => number_format(
                $plin,
                2,
                '.',
                ''
            ),
            'total_vendido' => number_format(
                $totalVendido,
                2,
                '.',
                ''
            ),
            'efectivo_esperado' => number_format(
                $efectivoEsperado,
                2,
                '.',
                ''
            ),
        ];
    }
}
