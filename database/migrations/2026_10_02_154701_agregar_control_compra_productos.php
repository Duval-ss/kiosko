<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('unidad_compra')->default('UNIDAD')->after('nombre');
            $table->integer('cantidad_por_paquete')->default(1)->after('unidad_compra');
            $table->decimal('costo_unitario', 10, 2)->default(0)->after('precio_compra');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'unidad_compra',
                'cantidad_por_paquete',
                'costo_unitario',
            ]);
        });
    }
};