<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\MovimientoInventarioController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// Estado de la API
Route::get('/estado', function () {
    return response()->json([
        'success' => true,

        'message' =>
            'La API de Kiosko Junior funciona correctamente.',

        'data' => [
            'sistema' => 'Kiosko Junior',
            'backend' => 'Laravel 12',
            'base_datos' => 'MySQL',
            'estado' => 'activo',
        ],
    ]);
});

// Login
Route::post(
    '/login',
    [AuthController::class, 'login']
);


/*
|--------------------------------------------------------------------------
| Productos y categorías públicas
|--------------------------------------------------------------------------
|
| El cliente puede ver productos y categorías
| sin necesidad de iniciar sesión.
|
*/

Route::apiResource(
    'categorias',
    CategoriaController::class
)->only([
    'index',
    'show',
]);

Route::apiResource(
    'productos',
    ProductoController::class
)->only([
    'index',
    'show',
]);


/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Usuario autenticado
    Route::get(
        '/usuario',
        [AuthController::class, 'usuario']
    );

    // Cerrar sesión
    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    );


    /*
    |--------------------------------------------------------------------------
    | Administrador y vendedor
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'rol:ADMINISTRADOR,VENDEDOR'
    )->group(function () {

        // Ventas
        Route::apiResource(
            'ventas',
            VentaController::class
        )->only([
            'index',
            'store',
            'show',
        ]);
    });


    /*
    |--------------------------------------------------------------------------
    | Solo administrador
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'rol:ADMINISTRADOR'
    )->group(function () {

        // Crear, editar y eliminar categorías
        Route::apiResource(
            'categorias',
            CategoriaController::class
        )->only([
            'store',
            'update',
            'destroy',
        ]);

        // Crear, editar y eliminar productos
        Route::apiResource(
            'productos',
            ProductoController::class
        )->only([
            'store',
            'update',
            'destroy',
        ]);

        // Movimientos de inventario
        Route::apiResource(
            'movimientos',
            MovimientoInventarioController::class
        )->only([
            'index',
            'store',
            'show',
        ]);
    });
});