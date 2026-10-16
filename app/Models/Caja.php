<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
        'usuario_id',
        'monto_inicial',
        'total_ventas',
        'monto_cierre',
        'fecha_apertura',
        'fecha_cierre',
        'estado',
    ];

    protected $casts = [
        'monto_inicial' => 'decimal:2',
        'total_ventas' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}