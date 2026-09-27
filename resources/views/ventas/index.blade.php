@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Ventas</h1>

            <p class="text-muted mb-0">
                Consulta las ventas registradas.
            </p>
        </div>

        <a
            href="{{ route('ventas.create') }}"
            class="btn btn-success"
        >
            Nueva venta
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form
                action="{{ route('ventas.index') }}"
                method="GET"
                class="row g-3"
            >
                <div class="col-md-4">
                    <label for="buscar" class="form-label">
                        Número de venta
                    </label>

                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        class="form-control"
                        value="{{ request('buscar') }}"
                        placeholder="Ejemplo: VTA-20260824"
                    >
                </div>

                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label">
                        Desde
                    </label>

                    <input
                        type="date"
                        id="fecha_inicio"
                        name="fecha_inicio"
                        class="form-control"
                        value="{{ request('fecha_inicio') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label">
                        Hasta
                    </label>

                    <input
                        type="date"
                        id="fecha_fin"
                        name="fecha_fin"
                        class="form-control"
                        value="{{ request('fecha_fin') }}"
                    >
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-primary">
                        Buscar
                    </button>

                    <a
                        href="{{ route('ventas.index') }}"
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
                            <th class="ps-4">Número</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr>
                                <td class="ps-4 fw-semibold">
                                    {{ $venta->numero_venta }}
                                </td>

                                <td>
                                    {{ $venta->fecha->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{ $venta->detalles_count }}
                                </td>

                                <td class="fw-semibold">
                                    S/ {{ number_format($venta->total, 2) }}
                                </td>

                                <td>
                                    <span class="badge text-bg-success">
                                        {{ $venta->estado }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <a
                                        href="{{ route('ventas.show', $venta) }}"
                                        class="btn btn-sm btn-primary"
                                    >
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="6"
                                    class="text-center py-5 text-muted"
                                >
                                    Todavía no existen ventas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($ventas->hasPages())
            <div class="card-footer bg-white">
                {{ $ventas->links() }}
            </div>
        @endif
    </div>
@endsection