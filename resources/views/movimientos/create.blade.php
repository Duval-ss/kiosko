@extends('layouts.app')

@section('title', 'Registrar movimiento')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h1 class="h4 mb-0">
                        Registrar movimiento de inventario
                    </h1>
                </div>

                <div class="card-body">
                    @if ($productos->isEmpty())
                        <div class="alert alert-warning">
                            No existen productos activos.
                            Primero debes registrar un producto.
                        </div>

                        <a
                            href="{{ route('productos.create') }}"
                            class="btn btn-success"
                        >
                            Registrar producto
                        </a>
                    @else
                        <form
                            action="{{ route('movimientos.store') }}"
                            method="POST"
                        >
                            @csrf

                            <div class="mb-3">
                                <label
                                    for="producto_id"
                                    class="form-label"
                                >
                                    Producto
                                </label>

                                <select
                                    id="producto_id"
                                    name="producto_id"
                                    class="form-select
                                        @error('producto_id')
                                            is-invalid
                                        @enderror"
                                    required
                                >
                                    <option value="">
                                        Seleccione un producto
                                    </option>

                                    @foreach ($productos as $producto)
                                        <option
                                            value="{{ $producto->id }}"
                                            data-stock="{{ $producto->stock }}"
                                            @selected(
                                                old('producto_id')
                                                == $producto->id
                                            )
                                        >
                                            {{ $producto->nombre }}
                                            — Stock: {{ $producto->stock }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('producto_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div
                                    id="stockActual"
                                    class="form-text fw-semibold"
                                ></div>
                            </div>

                            <div class="mb-3">
                                <label for="tipo" class="form-label">
                                    Tipo de movimiento
                                </label>

                                <select
                                    id="tipo"
                                    name="tipo"
                                    class="form-select
                                        @error('tipo')
                                            is-invalid
                                        @enderror"
                                    required
                                >
                                    <option value="">
                                        Seleccione el tipo
                                    </option>

                                    <option
                                        value="ENTRADA"
                                        @selected(
                                            old('tipo') === 'ENTRADA'
                                        )
                                    >
                                        Entrada de productos
                                    </option>

                                    <option
                                        value="SALIDA"
                                        @selected(
                                            old('tipo') === 'SALIDA'
                                        )
                                    >
                                        Salida de productos
                                    </option>

                                    <option
                                        value="AJUSTE"
                                        @selected(
                                            old('tipo') === 'AJUSTE'
                                        )
                                    >
                                        Ajuste de inventario
                                    </option>
                                </select>

                                @error('tipo')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label
                                    id="etiquetaCantidad"
                                    for="cantidad"
                                    class="form-label"
                                >
                                    Cantidad
                                </label>

                                <input
                                    type="number"
                                    id="cantidad"
                                    name="cantidad"
                                    class="form-control
                                        @error('cantidad')
                                            is-invalid
                                        @enderror"
                                    value="{{ old('cantidad') }}"
                                    min="0"
                                    required
                                >

                                @error('cantidad')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div
                                    id="ayudaCantidad"
                                    class="form-text"
                                >
                                    Ingrese la cantidad del movimiento.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label
                                    for="motivo"
                                    class="form-label"
                                >
                                    Motivo
                                </label>

                                <input
                                    type="text"
                                    id="motivo"
                                    name="motivo"
                                    class="form-control
                                        @error('motivo')
                                            is-invalid
                                        @enderror"
                                    value="{{ old('motivo') }}"
                                    maxlength="255"
                                    placeholder="Ejemplo: Compra de mercadería"
                                    required
                                >

                                @error('motivo')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label
                                    for="referencia"
                                    class="form-label"
                                >
                                    Referencia
                                </label>

                                <input
                                    type="text"
                                    id="referencia"
                                    name="referencia"
                                    class="form-control
                                        @error('referencia')
                                            is-invalid
                                        @enderror"
                                    value="{{ old('referencia') }}"
                                    maxlength="100"
                                    placeholder="Ejemplo: Compra 001"
                                >

                                @error('referencia')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-success"
                                >
                                    Registrar movimiento
                                </button>

                                <a
                                    href="{{ route('movimientos.index') }}"
                                    class="btn btn-secondary"
                                >
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const productoSelect =
            document.getElementById('producto_id');

        const tipoSelect =
            document.getElementById('tipo');

        const stockActual =
            document.getElementById('stockActual');

        const etiquetaCantidad =
            document.getElementById('etiquetaCantidad');

        const ayudaCantidad =
            document.getElementById('ayudaCantidad');

        function actualizarStock() {
            const opcion =
                productoSelect.options[productoSelect.selectedIndex];

            const stock = opcion?.dataset.stock;

            stockActual.textContent = stock !== undefined
                ? `Stock actual: ${stock}`
                : '';
        }

        function actualizarTipo() {
            if (tipoSelect.value === 'AJUSTE') {
                etiquetaCantidad.textContent = 'Nuevo stock';

                ayudaCantidad.textContent =
                    'Escribe la cantidad total que debe quedar.';
            } else {
                etiquetaCantidad.textContent = 'Cantidad';

                ayudaCantidad.textContent =
                    'Escribe la cantidad que ingresará o saldrá.';
            }
        }

        productoSelect?.addEventListener(
            'change',
            actualizarStock
        );

        tipoSelect?.addEventListener(
            'change',
            actualizarTipo
        );

        actualizarStock();
        actualizarTipo();
    </script>
@endpush