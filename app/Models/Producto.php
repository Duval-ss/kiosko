<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'tipo',
        'categoria_id',
        'codigo_barras',
        'nombre',
        'descripcion',

        // Compra
        'precio_compra',
        'unidad_compra',
        'cantidad_por_paquete',
        'costo_unitario',

        // Venta
        'precio_venta',

        // Inventario
        'stock',
        'stock_minimo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'costo_unitario' => 'decimal:2',

            'cantidad_por_paquete' => 'integer',

            'stock' => 'integer',
            'stock_minimo' => 'integer',

            'estado' => 'boolean',
        ];
    }

    /**
     * Categoría a la que pertenece el producto o comida.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Movimientos de inventario.
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Detalles de las compras realizadas.
     */
    public function detallesCompra(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Determina si es un producto comercial.
     */
    public function esProducto(): bool
    {
        return $this->tipo === 'PRODUCTO';
    }

    /**
     * Determina si es una comida.
     */
    public function esComida(): bool
    {
        return $this->tipo === 'COMIDA';
    }

    /**
     * Indica si el artículo utiliza código de barras.
     */
    public function usaCodigoBarras(): bool
    {
        return $this->tipo === 'PRODUCTO';
    }

    /**
     * Indica si el artículo maneja precio de compra.
     */
    public function manejaPrecioCompra(): bool
    {
        return $this->tipo === 'PRODUCTO';
    }
}