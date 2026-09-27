@extends('layouts.app')

@section('title', 'Detalle de venta')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                {{ $venta->numero_venta }}
            </h1>

            <p class="text-muted mb-0">
                Registrada el
                {{ $venta->fecha->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <a
                href="{{ route('ventas.index') }}"
                class="btn btn-outline-secondary"
            >
                Volver
            </a>

            <a
                href="{{ route('ventas.create') }}"
                class="btn btn-success"
            >
                Nueva venta
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 mb-0">
                        Productos vendidos
                    </h2>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th class="pe-4">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($venta->detalles as $detalle)
                                    <tr>
                                        <td class="ps-4 fw-semibold">
                                            {{ $detalle->producto->nombre }}
                                        </td>

                                        <td>
                                            S/
                                            {{ number_format(
                                                $detalle->precio_unitario,
                                                2
                                            ) }}
                                        </td>

                                        <td>
                                            {{ $detalle->cantidad }}
                                        </td>

                                        <td class="pe-4 fw-semibold">
                                            S/
                                            {{ number_format(
                                                $detalle->subtotal,
                                                2
                                            ) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 mb-0">
                        Resumen
                    </h2>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Número:</span>

                        <strong>
                            {{ $venta->numero_venta }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Estado:</span>

                        <span class="badge text-bg-success">
                            {{ $venta->estado }}
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="fs-5">Total:</span>

                        <strong class="fs-4 text-success">
                            S/ {{ number_format($venta->total, 2) }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection