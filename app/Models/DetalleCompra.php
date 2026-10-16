<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $table = 'detalle_compras';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad_paquetes',
        'unidades_por_paquete',
        'unidades_ingresadas',
        'precio_paquete',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cantidad_paquetes' => 'integer',
            'unidades_por_paquete' => 'integer',
            'unidades_ingresadas' => 'integer',
            'precio_paquete' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}