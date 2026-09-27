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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('numero_venta', 30)->unique();

            $table->dateTime('fecha');

            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);

            $table->string('estado', 20)
                ->default('COMPLETADA');

            $table->timestamps();

            $table->index('fecha');
            $table->index('estado');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};