<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CajaController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MovimientoInventarioController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/estado', function () {
    return response()->json([
        'success' => true,
        'message' => 'La API de Kiosko Junior funciona correctamente.',
        'data' => [
            'sistema' => 'Kiosko Junior',
            'backend' => 'Laravel 12',
            'base_datos' => 'MySQL',
            'estado' => 'activo',
        ],
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Productos y categorías públicas
|--------------------------------------------------------------------------
*/

Route::apiResource('categorias', CategoriaController::class)
    ->only(['index', 'show']);

Route::apiResource('productos', ProductoController::class)
    ->only(['index', 'show']);

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Usuario autenticado
    Route::get('/usuario', [AuthController::class, 'usuario']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Administrador y vendedor
    |--------------------------------------------------------------------------
    */

    Route::middleware('rol:ADMINISTRADOR,VENDEDOR')->group(function () {

        // Caja
        Route::get('/caja/actual', [CajaController::class, 'actual']);
        Route::post('/caja/abrir', [CajaController::class, 'abrir']);
        Route::post('/caja/cerrar', [CajaController::class, 'cerrar']);
        Route::get('/caja/resumen', [CajaController::class, 'resumen']);

        // Resumen de ventas
        Route::get('/ventas/resumen', [VentaController::class, 'resumen']);

        // Ventas
        Route::apiResource('ventas', VentaController::class)
            ->only(['index', 'store', 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | Solo administrador
    |--------------------------------------------------------------------------
    */

    Route::middleware('rol:ADMINISTRADOR')->group(function () {

        // Dashboard financiero
        Route::get(
            '/dashboard/resumen',
            [DashboardController::class, 'resumen']
        );

        // Categorías
        Route::apiResource('categorias', CategoriaController::class)
            ->only(['store', 'update', 'destroy']);

        // Productos
        Route::apiResource('productos', ProductoController::class)
            ->only(['store', 'update', 'destroy']);

        // Movimientos de inventario
        Route::apiResource('movimientos', MovimientoInventarioController::class)
            ->only(['index', 'store', 'show']);

        // Compras
        Route::get('/compras', [CompraController::class, 'index']);
        Route::post('/compras', [CompraController::class, 'store']);
        Route::get('/compras/{id}', [CompraController::class, 'show']);
    });
});
