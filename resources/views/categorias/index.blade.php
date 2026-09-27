@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >
        <div>
            <h1 class="h3 mb-1">
                Categorías
            </h1>

            <p class="text-muted mb-0">
                Administra las categorías de los productos.
            </p>
        </div>

        <a
            href="{{ route('categorias.create') }}"
            class="btn btn-success"
        >
            Nueva categoría
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categorias as $categoria)
                            <tr>
                                <td class="ps-4">
                                    {{ $categoria->id }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $categoria->nombre }}
                                </td>

                                <td>
                                    {{ $categoria->descripcion ?: 'Sin descripción' }}
                                </td>

                                <td>
                                    @if ($categoria->estado)
                                        <span class="badge text-bg-success">
                                            Activa
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <div
                                        class="d-inline-flex gap-2"
                                    >
                                        <a
                                            href="{{ route('categorias.edit', $categoria) }}"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('categorias.destroy', $categoria) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Deseas eliminar esta categoría?')"
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
                                    colspan="5"
                                    class="text-center py-5 text-muted"
                                >
                                    Todavía no existen categorías registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($categorias->hasPages())
            <div class="card-footer bg-white">
                {{ $categorias->links() }}
            </div>
        @endif
    </div>
@endsection