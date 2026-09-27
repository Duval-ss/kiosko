<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create(
            'detalle_ventas',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('venta_id')
                    ->constrained('ventas')
                    ->cascadeOnDelete();

                $table->foreignId('producto_id')
                    ->constrained('productos')
                    ->restrictOnDelete();

                $table->unsignedInteger('cantidad');

                $table->decimal(
                    'precio_unitario',
                    10,
                    2
                );

                $table->decimal(
                    'subtotal',
                    12,
                    2
                );

                $table->timestamps();

                $table->unique([
                    'venta_id',
                    'producto_id',
                ]);
            }
        );
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};