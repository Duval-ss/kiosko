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
        /*
        |--------------------------------------------------------------------------
        | La tabla cajas ya puede existir
        |--------------------------------------------------------------------------
        |
        | Si ya existe, no hacemos nada para no borrar ni duplicar
        | la información existente.
        |
        */

        if (!Schema::hasTable('cajas')) {
            Schema::create('cajas', function (Blueprint $table) {
                $table->id();

                $table->foreignId('usuario_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->decimal('monto_inicial', 10, 2)
                    ->default(0);

                $table->decimal('total_ventas', 10, 2)
                    ->default(0);

                $table->decimal('monto_cierre', 10, 2)
                    ->nullable();

                $table->dateTime('fecha_apertura');

                $table->dateTime('fecha_cierre')
                    ->nullable();

                $table->enum('estado', [
                    'ABIERTA',
                    'CERRADA'
                ])->default('ABIERTA');

                $table->timestamps();
            });
        }
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};