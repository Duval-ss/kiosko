<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'user_id',
        'caja_id',
        'numero_venta',
        'fecha',
        'subtotal',
        'total',
        'metodo_pago',
        'monto_recibido',
        'vuelto',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'monto_recibido' => 'decimal:2',
            'vuelto' => 'decimal:2',
        ];
    }

    /**
     * Usuario que registró la venta.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Caja en la que se registró la venta.
     */
    public function caja(): BelongsTo
    {
        return $this->belongsTo(
            Caja::class,
            'caja_id'
        );
    }

    /**
     * Detalles de productos de la venta.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(
            DetalleVenta::class
        );
    }
}
