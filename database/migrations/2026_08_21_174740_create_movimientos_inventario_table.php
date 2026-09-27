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
            'movimientos_inventario',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('producto_id')
                    ->constrained('productos')
                    ->restrictOnDelete();

                $table->string('tipo', 20);
                $table->unsignedInteger('cantidad');

                $table->unsignedInteger('stock_anterior');
                $table->unsignedInteger('stock_nuevo');

                $table->string('motivo', 255);
                $table->string('referencia', 100)->nullable();

                $table->timestamps();

                $table->index(['tipo', 'created_at']);
            }
        );
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};