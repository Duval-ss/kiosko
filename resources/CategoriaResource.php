<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    /**
     * Transformar el recurso en un arreglo.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado' => (bool) $this->estado,
            'fecha_registro' => $this->created_at,
            'fecha_actualizacion' => $this->updated_at,
        ];
    }
}