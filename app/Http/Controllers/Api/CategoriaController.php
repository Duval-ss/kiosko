<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Lista las categorías.
     */
    public function index(Request $request): JsonResponse
    {
        $categorias = Categoria::query()
            ->when(
                $request->buscar,
                function ($consulta, $buscar) {
                    $consulta->where(
                        'nombre',
                        'like',
                        "%{$buscar}%"
                    );
                }
            )
            ->when(
                $request->filled('estado'),
                function ($consulta) use ($request) {
                    $consulta->where(
                        'estado',
                        $request->boolean('estado')
                    );
                }
            )
            ->orderBy('nombre')
            ->paginate(10);

        return response()->json([
            'success' => true,

            'message' =>
                'Categorías consultadas correctamente.',

            'data' => CategoriaResource::collection(
                $categorias->items()
            ),

            'pagination' => [
                'pagina_actual' =>
                    $categorias->currentPage(),

                'ultima_pagina' =>
                    $categorias->lastPage(),

                'por_pagina' =>
                    $categorias->perPage(),

                'total' =>
                    $categorias->total(),
            ],
        ]);
    }

    /**
     * Registra una categoría.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:categorias,nombre',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],

            'estado' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $datos['estado'] = $datos['estado'] ?? true;

        $categoria = Categoria::create($datos);

        return response()->json([
            'success' => true,

            'message' =>
                'Categoría registrada correctamente.',

            'data' => new CategoriaResource($categoria),
        ], 201);
    }

    /**
     * Consulta una categoría.
     */
    public function show(
        Categoria $categoria
    ): JsonResponse {
        return response()->json([
            'success' => true,

            'message' =>
                'Categoría consultada correctamente.',

            'data' => new CategoriaResource($categoria),
        ]);
    }

    /**
     * Actualiza una categoría.
     */
    public function update(
        Request $request,
        Categoria $categoria
    ): JsonResponse {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'categorias',
                    'nombre'
                )->ignore($categoria->id),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],

            'estado' => [
                'required',
                'boolean',
            ],
        ]);

        $categoria->update($datos);

        return response()->json([
            'success' => true,

            'message' =>
                'Categoría actualizada correctamente.',

            'data' => new CategoriaResource(
                $categoria->fresh()
            ),
        ]);
    }

    /**
     * Elimina una categoría sin productos.
     */
    public function destroy(
        Categoria $categoria
    ): JsonResponse {
        if ($categoria->productos()->exists()) {
            return response()->json([
                'success' => false,

                'message' =>
                    'No se puede eliminar la categoría porque tiene productos registrados.',
            ], 409);
        }

        $categoria->delete();

        return response()->json([
            'success' => true,

            'message' =>
                'Categoría eliminada correctamente.',

            'data' => null,
        ]);
    }
}