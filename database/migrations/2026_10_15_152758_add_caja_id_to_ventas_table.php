<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('caja_id')
                ->nullable()
                ->after('user_id')
                ->constrained('cajas')
                ->nullOnDelete();

            $table->index(
                ['caja_id', 'fecha'],
                'ventas_caja_fecha_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex('ventas_caja_fecha_index');
            $table->dropConstrainedForeignId('caja_id');
        });
    }
};
