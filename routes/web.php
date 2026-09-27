<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('categorias.index');
});

Route::resource(
    'categorias',
    CategoriaController::class
)->except([
    'show',
]);

Route::resource(
    'productos',
    ProductoController::class
)->except([
    'show',
]);

Route::resource(
    'movimientos',
    MovimientoInventarioController::class
)->only([
    'index',
    'create',
    'store',
]);

Route::resource(
    'ventas',
    VentaController::class
)->only([
    'index',
    'create',
    'store',
    'show',
]);