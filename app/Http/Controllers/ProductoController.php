<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $productos = Producto::with('categoria')
            ->when($request->buscar, function ($consulta, $buscar) {
                $consulta->where(function ($subconsulta) use ($buscar) {
                    $subconsulta
                        ->where(
                            'nombre',
                            'like',
                            "%{$buscar}%"
                        )
                        ->orWhere(
                            'codigo_barras',
                            'like',
                            "%{$buscar}%"
                        );
                });
            })
            ->when(
                $request->categoria_id,
                function ($consulta, $categoriaId) {
                    $consulta->where(
                        'categoria_id',
                        $categoriaId
                    );
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categorias = Categoria::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'productos.index',
            compact('productos', 'categorias')
        );
    }

    public function create(): View
    {
        $categorias = Categoria::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'productos.create',
            compact('categorias')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarProducto($request);

        Producto::create($datos);

        return redirect()
            ->route('productos.index')
            ->with(
                'success',
                'Producto registrado correctamente.'
            );
    }

    public function edit(Producto $producto): View
    {
        $categorias = Categoria::where('estado', true)
            ->orWhere('id', $producto->categoria_id)
            ->orderBy('nombre')
            ->get();

        return view(
            'productos.edit',
            compact('producto', 'categorias')
        );
    }

    public function update(
        Request $request,
        Producto $producto
    ): RedirectResponse {
        $datos = $this->validarProducto(
            $request,
            $producto
        );

        $producto->update($datos);

        return redirect()
            ->route('productos.index')
            ->with(
                'success',
                'Producto actualizado correctamente.'
            );
    }

    public function destroy(
        Producto $producto
    ): RedirectResponse {
        if ($producto->movimientosInventario()->exists()) {
            return redirect()
                ->route('productos.index')
                ->with(
                    'error',
                    'No se puede eliminar el producto porque tiene movimientos de inventario registrados. Puedes editarlo y cambiar su estado a inactivo.'
                );
        }

        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with(
                'success',
                'Producto eliminado correctamente.'
            );
    }

    private function validarProducto(
        Request $request,
        ?Producto $producto = null
    ): array {
        return $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],

            'codigo_barras' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'productos',
                    'codigo_barras'
                )->ignore($producto?->id),
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],

            'precio_venta' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'stock_minimo' => [
                'required',
                'integer',
                'min:0',
            ],

            'estado' => [
                'required',
                'boolean',
            ],
        ]);
    }
}