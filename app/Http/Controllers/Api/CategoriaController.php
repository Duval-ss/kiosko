<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'tipo' => [
                'required',
                Rule::in(['PRODUCTO', 'COMIDA'])
            ],
            'estado' => 'nullable|boolean',
        ]);

        $categoria = Categoria::create([
            'nombre' => trim($datos['nombre']),
            'descripcion' => $datos['descripcion'] ?? null,
            'tipo' => $datos['tipo'],
            'estado' => $datos['estado'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada correctamente',
            'data' => $categoria
        ], 201);
    }

    public function show(Categoria $categoria)
    {
        return response()->json([
            'success' => true,
            'data' => $categoria
        ]);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'tipo' => [
                'required',
                Rule::in(['PRODUCTO', 'COMIDA'])
            ],
            'estado' => 'nullable|boolean',
        ]);

        $categoria->update([
            'nombre' => trim($datos['nombre']),
            'descripcion' => $datos['descripcion'] ?? null,
            'tipo' => $datos['tipo'],
            'estado' => $datos['estado'] ?? $categoria->estado,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada correctamente',
            'data' => $categoria->fresh()
        ]);
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar esta categoría porque tiene artículos asociados.'
            ], 422);
        }

        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente'
        ]);
    }
}