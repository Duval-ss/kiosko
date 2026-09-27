@extends('layouts.app')

@section('title', 'Movimientos de inventario')

@section('content')
    <div
        class="d-flex justify-content-between
            align-items-center mb-4"
    >
        <div>
            <h1 class="h3 mb-1">
                Movimientos de inventario
            </h1>

            <p class="text-muted mb-0">
                Historial de entradas, salidas y ajustes.
            </p>
        </div>

        <a
            href="{{ route('movimientos.create') }}"
            class="btn btn-success"
        >
            Registrar movimiento
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form
                action="{{ route('movimientos.index') }}"
                method="GET"
                class="row g-3"
            >
                <div class="col-md-5">
                    <label
                        for="producto_id"
                        class="form-label"
                    >
                        Producto
                    </label>

                    <select
                        id="producto_id"
                        name="producto_id"
                        class="form-select"
                    >
                        <option value="">
                            Todos los productos
                        </option>

                        @foreach ($productos as $producto)
                            <option
                                value="{{ $producto->id }}"
                                @selected(
                                    request('producto_id')
                                    == $producto->id
                                )
                            >
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="tipo" class="form-label">
                        Tipo
                    </label>

                    <select
                        id="tipo"
                        name="tipo"
                        class="form-select"
                    >
                        <option value="">
                            Todos los tipos
                        </option>

                        <option
                            value="ENTRADA"
                            @selected(
                                request('tipo') === 'ENTRADA'
                            )
                        >
                            Entrada
                        </option>

                        <option
                            value="SALIDA"
                            @selected(
                                request('tipo') === 'SALIDA'
                            )
                        >
                            Salida
                        </option>

                        <option
                            value="AJUSTE"
                            @selected(
                                request('tipo') === 'AJUSTE'
                            )
                        >
                            Ajuste
                        </option>
                    </select>
                </div>

                <div
                    class="col-md-3 d-flex
                        align-items-end gap-2"
                >
                    <button class="btn btn-primary">
                        Filtrar
                    </button>

                    <a
                        href="{{ route('movimientos.index') }}"
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
                <table
                    class="table table-hover align-middle mb-0"
                >
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Stock anterior</th>
                            <th>Stock nuevo</th>
                            <th>Motivo</th>
                            <th class="pe-4">Referencia</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($movimientos as $movimiento)
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    {{ $movimiento->created_at
                                        ->format('d/m/Y H:i') }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $movimiento->producto->nombre }}
                                </td>

                                <td>
                                    @if ($movimiento->tipo === 'ENTRADA')
                                        <span
                                            class="badge text-bg-success"
                                        >
                                            Entrada
                                        </span>
                                    @elseif ($movimiento->tipo === 'SALIDA')
                                        <span
                                            class="badge text-bg-danger"
                                        >
                                            Salida
                                        </span>
                                    @else
                                        <span
                                            class="badge text-bg-warning"
                                        >
                                            Ajuste
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $movimiento->cantidad }}
                                </td>

                                <td>
                                    {{ $movimiento->stock_anterior }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $movimiento->stock_nuevo }}
                                </td>

                                <td>
                                    {{ $movimiento->motivo }}
                                </td>

                                <td class="pe-4">
                                    {{ $movimiento->referencia
                                        ?: 'Sin referencia' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="8"
                                    class="text-center
                                        py-5 text-muted"
                                >
                                    Todavía no existen movimientos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($movimientos->hasPages())
            <div class="card-footer bg-white">
                {{ $movimientos->links() }}
            </div>
        @endif
    </div>
@endsection