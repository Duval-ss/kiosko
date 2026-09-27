<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega los campos necesarios para el POS.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('metodo_pago', 20)
                ->default('EFECTIVO')
                ->after('total');

            $table->decimal('monto_recibido', 12, 2)
                ->default(0)
                ->after('metodo_pago');

            $table->decimal('vuelto', 12, 2)
                ->default(0)
                ->after('monto_recibido');
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->decimal('precio_compra_unitario', 10, 2)
                ->default(0)
                ->after('cantidad');
        });
    }

    /**
     * Revierte los cambios.
     */
    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn('precio_compra_unitario');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'metodo_pago',
                'monto_recibido',
                'vuelto',
            ]);
        });
    }
};