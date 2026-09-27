<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuarios
        $administrador = User::firstOrCreate(
            ['email' => 'admin@kiosko.com'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'rol' => User::ROL_ADMINISTRADOR,
            ]
        );

        if ($administrador->rol !== User::ROL_ADMINISTRADOR) {
            $administrador->update([
                'rol' => User::ROL_ADMINISTRADOR,
            ]);
        }

        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@kiosko.com'],
            [
                'name' => 'Vendedor',
                'password' => 'password',
                'rol' => User::ROL_VENDEDOR,
            ]
        );

        if ($vendedor->rol !== User::ROL_VENDEDOR) {
            $vendedor->update([
                'rol' => User::ROL_VENDEDOR,
            ]);
        }

        // Categorías
        $golosinas = Categoria::firstOrCreate(
            ['nombre' => 'Golosinas'],
            ['descripcion' => 'Caramelos, chocolates y dulces.']
        );

        $bebidas = Categoria::firstOrCreate(
            ['nombre' => 'Bebidas'],
            ['descripcion' => 'Gaseosas, jugos y aguas.']
        );

        // Productos
        Producto::firstOrCreate(
            ['codigo_barras' => '7750001'],
            [
                'categoria_id' => $golosinas->id,
                'nombre' => 'Chocolate personal',
                'descripcion' => 'Chocolate con relleno.',
                'precio_compra' => 1.5,
                'precio_venta' => 3,
                'stock' => 20,
                'stock_minimo' => 5,
                'estado' => true,
            ]
        );

        Producto::firstOrCreate(
            ['codigo_barras' => '7750002'],
            [
                'categoria_id' => $golosinas->id,
                'nombre' => 'Galleta de chocolate',
                'descripcion' => 'Galleta suave con chispas.',
                'precio_compra' => 1,
                'precio_venta' => 2.5,
                'stock' => 30,
                'stock_minimo' => 5,
                'estado' => true,
            ]
        );

        Producto::firstOrCreate(
            ['codigo_barras' => '7750003'],
            [
                'categoria_id' => $bebidas->id,
                'nombre' => 'Gaseosa personal',
                'descripcion' => 'Gaseosa de 500 ml.',
                'precio_compra' => 1.8,
                'precio_venta' => 3.5,
                'stock' => 25,
                'stock_minimo' => 8,
                'estado' => true,
            ]
        );
    }
}