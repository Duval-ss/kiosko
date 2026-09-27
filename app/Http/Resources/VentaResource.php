<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    /**
     * Convierte la venta en una respuesta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'numero_venta' => $this->numero_venta,

            'fecha' => $this->fecha?->format(
                'Y-m-d H:i:s'
            ),

            'subtotal' => number_format(
                (float) $this->subtotal,
                2,
                '.',
                ''
            ),

            'total' => number_format(
                (float) $this->total,
                2,
                '.',
                ''
            ),

            'metodo_pago' => $this->metodo_pago,

            'monto_recibido' => number_format(
                (float) $this->monto_recibido,
                2,
                '.',
                ''
            ),

            'vuelto' => number_format(
                (float) $this->vuelto,
                2,
                '.',
                ''
            ),

            'estado' => $this->estado,

            'usuario' => $this->usuario
                ? [
                    'id' => $this->usuario->id,
                    'nombre' => $this->usuario->name,
                    'email' => $this->usuario->email,
                    'rol' => $this->usuario->rol,
                ]
                : null,

            'detalles' =>
                DetalleVentaResource::collection(
                    $this->detalles
                ),

            'cantidad_productos' =>
                $this->detalles->count(),

            'fecha_registro' =>
                $this->created_at?->format(
                    'Y-m-d H:i:s'
                ),
        ];
    }
}