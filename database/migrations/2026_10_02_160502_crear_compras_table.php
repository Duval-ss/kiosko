<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('numero_compra')->unique();

            $table->string('proveedor')->nullable();

            $table->dateTime('fecha');

            $table->decimal('total', 10, 2)->default(0);

            $table->enum('estado', [
                'REGISTRADA',
                'ANULADA'
            ])->default('REGISTRADA');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};