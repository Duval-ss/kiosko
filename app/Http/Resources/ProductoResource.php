<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'codigo_barras' => $this->codigo_barras,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'precio_compra' => $this->precio_compra,

            'precio_venta' => $this->precio_venta,

            'stock' => $this->stock,

            'stock_minimo' => $this->stock_minimo,

            'stock_bajo' =>
                $this->stock <= $this->stock_minimo,

            'estado' => $this->estado,

            'categoria' => new CategoriaResource(
                $this->whenLoaded('categoria')
            ),

            'fecha_registro' => $this->created_at
                ?->format('Y-m-d H:i:s'),

            'fecha_actualizacion' => $this->updated_at
                ?->format('Y-m-d H:i:s'),
        ];
    }
}