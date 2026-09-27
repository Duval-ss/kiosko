@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >
        <div>
            <h1 class="h3 mb-1">Productos</h1>

            <p class="text-muted mb-0">
                Administra el inventario del Kiosko Junior.
            </p>
        </div>

        <a
            href="{{ route('productos.create') }}"
            class="btn btn-success"
        >
            Nuevo producto
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form
                action="{{ route('productos.index') }}"
                method="GET"
                class="row g-3"
            >
                <div class="col-md-5">
                    <label for="buscar" class="form-label">
                        Buscar
                    </label>

                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        class="form-control"
                        value="{{ request('buscar') }}"
                        placeholder="Nombre o código de barras"
                    >
                </div>

                <div class="col-md-4">
                    <label for="categoria_id" class="form-label">
                        Categoría
                    </label>

                    <select
                        id="categoria_id"
                        name="categoria_id"
                        class="form-select"
                    >
                        <option value="">Todas las categorías</option>

                        @foreach ($categorias as $categoria)
                            <option
                                value="{{ $categoria->id }}"
                                @selected(
                                    request('categoria_id') == $categoria->id
                                )
                            >
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary">
                        Buscar
                    </button>

                    <a
                        href="{{ route('productos.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td class="ps-4">
                                    {{ $producto->codigo_barras }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $producto->nombre }}
                                </td>

                                <td>
                                    {{ $producto->categoria->nombre }}
                                </td>

                                <td>
                                    S/ {{ number_format($producto->precio_venta, 2) }}
                                </td>

                                <td>
                                    @if ($producto->stock <= $producto->stock_minimo)
                                        <span class="badge text-bg-danger">
                                            {{ $producto->stock }} — Bajo
                                        </span>
                                    @else
                                        <span class="badge text-bg-success">
                                            {{ $producto->stock }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($producto->estado)
                                        <span class="badge text-bg-success">
                                            Activo
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <a
                                            href="{{ route('productos.edit', $producto) }}"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('productos.destroy', $producto) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Deseas eliminar este producto?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center py-5 text-muted"
                                >
                                    No se encontraron productos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($productos->hasPages())
            <div class="card-footer bg-white">
                {{ $productos->links() }}
            </div>
        @endif
    </div>
@endsection