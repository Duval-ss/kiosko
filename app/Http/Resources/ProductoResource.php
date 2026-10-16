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

            // PRODUCTO o COMIDA
            'tipo' => $this->tipo,

            // Código de barras
            'codigo_barras' => $this->codigo_barras,

            // Información básica
            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN DE COMPRA
            |--------------------------------------------------------------------------
            */

            // Precio que paga el dueño por el paquete
            'precio_compra' => $this->precio_compra,

            // Ejemplo: PAQUETE, CAJA, DOCENA
            'unidad_compra' => $this->unidad_compra,

            // Ejemplo: 12 unidades por paquete
            'cantidad_por_paquete' => $this->cantidad_por_paquete,

            // Costo real de una unidad
            'costo_unitario' => $this->costo_unitario,

            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN DE VENTA
            |--------------------------------------------------------------------------
            */

            // Precio al que se vende una unidad
            'precio_venta' => $this->precio_venta,

            // Ganancia por cada unidad vendida
            'ganancia_unitaria' => round(
                (float) $this->precio_venta -
                (float) $this->costo_unitario,
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | INVENTARIO
            |--------------------------------------------------------------------------
            */

            'stock' => $this->stock,

            'stock_minimo' => $this->stock_minimo,

            'stock_bajo' =>
                $this->stock <= $this->stock_minimo,

            'estado' => $this->estado,

            /*
            |--------------------------------------------------------------------------
            | CATEGORÍA
            |--------------------------------------------------------------------------
            */

            'categoria' => new CategoriaResource(
                $this->whenLoaded('categoria')
            ),

            /*
            |--------------------------------------------------------------------------
            | FECHAS
            |--------------------------------------------------------------------------
            */

            'fecha_registro' => $this->created_at
                ?->format('Y-m-d H:i:s'),

            'fecha_actualizacion' => $this->updated_at
                ?->format('Y-m-d H:i:s'),
        ];
    }
}