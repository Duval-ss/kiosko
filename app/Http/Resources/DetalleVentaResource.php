<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleVentaResource extends JsonResource
{
    /**
     * Convierte el detalle en JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'cantidad' =>
                (int) $this->cantidad,

            'precio_compra_unitario' =>
                number_format(
                    (float) $this->precio_compra_unitario,
                    2,
                    '.',
                    ''
                ),

            'precio_unitario' =>
                number_format(
                    (float) $this->precio_unitario,
                    2,
                    '.',
                    ''
                ),

            'subtotal' =>
                number_format(
                    (float) $this->subtotal,
                    2,
                    '.',
                    ''
                ),

            'utilidad' =>
                number_format(
                    (
                        (float) $this->precio_unitario
                        - (float) $this->precio_compra_unitario
                    ) * (int) $this->cantidad,
                    2,
                    '.',
                    ''
                ),

            'producto' => $this->whenLoaded(
                'producto',
                function () {
                    return [
                        'id' =>
                            $this->producto->id,

                        'codigo_barras' =>
                            $this->producto->codigo_barras,

                        'nombre' =>
                            $this->producto->nombre,

                        'precio_compra' =>
                            $this->producto->precio_compra,

                        'precio_venta' =>
                            $this->producto->precio_venta,

                        'stock_actual' =>
                            (int) $this->producto->stock,
                    ];
                }
            ),
        ];
    }
}