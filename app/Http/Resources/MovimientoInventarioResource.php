<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoInventarioResource extends JsonResource
{
    /**
     * Convierte el movimiento en una respuesta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'tipo' => $this->tipo,

            'cantidad' => (int) $this->cantidad,

            'stock_anterior' =>
                (int) $this->stock_anterior,

            'stock_nuevo' =>
                (int) $this->stock_nuevo,

            'motivo' => $this->motivo,

            'referencia' => $this->referencia,

            'producto' => [
                'id' => $this->producto->id,

                'codigo_barras' =>
                    $this->producto->codigo_barras,

                'nombre' =>
                    $this->producto->nombre,

                'stock_actual' =>
                    (int) $this->producto->stock,
            ],

            'fecha_registro' =>
                $this->created_at?->format(
                    'Y-m-d H:i:s'
                ),

            'fecha_actualizacion' =>
                $this->updated_at?->format(
                    'Y-m-d H:i:s'
                ),
        ];
    }
}