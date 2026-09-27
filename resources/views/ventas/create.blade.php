@extends('layouts.app')

@section('title', 'Registrar venta')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Registrar venta</h1>

            <p class="text-muted mb-0">
                Agrega productos mediante el código de barras o la lista.
            </p>
        </div>

        <a
            href="{{ route('ventas.index') }}"
            class="btn btn-outline-secondary"
        >
            Historial de ventas
        </a>
    </div>

    @if ($productos->isEmpty())
        <div class="alert alert-warning">
            No existen productos activos con stock disponible.
        </div>

        <a
            href="{{ route('productos.index') }}"
            class="btn btn-success"
        >
            Revisar productos
        </a>
    @else
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h2 class="h5 mb-0">
                            Agregar producto
                        </h2>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label
                                for="codigoBarras"
                                class="form-label"
                            >
                                Código de barras
                            </label>

                            <div class="input-group">
                                <input
                                    type="text"
                                    id="codigoBarras"
                                    class="form-control"
                                    placeholder="Escanea o escribe el código"
                                    autocomplete="off"
                                    autofocus
                                >

                                <button
                                    type="button"
                                    id="botonCodigo"
                                    class="btn btn-success"
                                >
                                    Agregar
                                </button>
                            </div>

                            <div class="form-text">
                                El lector USB funciona como un teclado.
                            </div>
                        </div>

                        <div class="text-center text-muted mb-3">
                            o
                        </div>

                        <div class="mb-3">
                            <label
                                for="productoSelect"
                                class="form-label"
                            >
                                Seleccionar producto
                            </label>

                            <select
                                id="productoSelect"
                                class="form-select"
                            >
                                <option value="">
                                    Seleccione un producto
                                </option>

                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}">
                                        {{ $producto->nombre }}
                                        — Stock: {{ $producto->stock }}
                                        — S/ {{ number_format($producto->precio_venta, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button
                            type="button"
                            id="botonSeleccionar"
                            class="btn btn-primary w-100"
                        >
                            Agregar seleccionado
                        </button>

                        <div
                            id="mensajeProducto"
                            class="alert d-none mt-3 mb-0"
                            role="alert"
                        ></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <form
                    id="formVenta"
                    action="{{ route('ventas.store') }}"
                    method="POST"
                >
                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h2 class="h5 mb-0">
                                Detalle de la venta
                            </h2>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table
                                    class="table table-hover align-middle mb-0"
                                >
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">
                                                Producto
                                            </th>
                                            <th>Precio</th>
                                            <th style="width: 130px;">
                                                Cantidad
                                            </th>
                                            <th>Subtotal</th>
                                            <th class="pe-4"></th>
                                        </tr>
                                    </thead>

                                    <tbody id="detalleVenta">
                                        <tr id="filaVacia">
                                            <td
                                                colspan="5"
                                                class="text-center py-5 text-muted"
                                            >
                                                Agrega productos a la venta.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white p-4">
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <div>
                                    <span class="text-muted">
                                        Total:
                                    </span>

                                    <strong
                                        id="totalVenta"
                                        class="fs-3 ms-2 text-success"
                                    >
                                        S/ 0.00
                                    </strong>
                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-success btn-lg"
                                >
                                    Registrar venta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    const catalogo = @json(
        $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'codigo_barras' => $producto->codigo_barras,
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio_venta,
                'stock' => $producto->stock,
            ];
        })->values()
    );

    const productosAnteriores = @json(old('productos', []));

    let carrito = [];

    const codigoBarras =
        document.getElementById('codigoBarras');

    const productoSelect =
        document.getElementById('productoSelect');

    const detalleVenta =
        document.getElementById('detalleVenta');

    const totalVenta =
        document.getElementById('totalVenta');

    const mensajeProducto =
        document.getElementById('mensajeProducto');

    function escapar(texto) {
        const elemento = document.createElement('div');
        elemento.textContent = texto;
        return elemento.innerHTML;
    }

    function mostrarMensaje(texto, tipo = 'danger') {
        mensajeProducto.textContent = texto;
        mensajeProducto.className =
            `alert alert-${tipo} mt-3 mb-0`;
    }

    function ocultarMensaje() {
        mensajeProducto.className = 'alert d-none mt-3 mb-0';
    }

    function agregarProducto(producto, cantidadInicial = 1) {
        ocultarMensaje();

        const existente = carrito.find(
            item => item.id === producto.id
        );

        if (existente) {
            if (existente.cantidad >= producto.stock) {
                mostrarMensaje(
                    `No hay más stock disponible de ${producto.nombre}.`
                );

                return;
            }

            existente.cantidad++;
        } else {
            carrito.push({
                ...producto,
                cantidad: Math.min(
                    Number(cantidadInicial),
                    producto.stock
                ),
            });
        }

        renderizarCarrito();
    }

    function agregarPorCodigo() {
        const codigo = codigoBarras.value.trim();

        if (!codigo) {
            mostrarMensaje('Escribe o escanea un código.');

            return;
        }

        const producto = catalogo.find(
            item => item.codigo_barras === codigo
        );

        if (!producto) {
            mostrarMensaje(
                'No se encontró un producto activo con ese código.'
            );

            codigoBarras.select();

            return;
        }

        agregarProducto(producto);

        codigoBarras.value = '';
        codigoBarras.focus();
    }

    function agregarSeleccionado() {
        const id = Number(productoSelect.value);

        const producto = catalogo.find(
            item => item.id === id
        );

        if (!producto) {
            mostrarMensaje('Selecciona un producto.');

            return;
        }

        agregarProducto(producto);

        productoSelect.value = '';
    }

    function cambiarCantidad(id, nuevaCantidad) {
        const producto = carrito.find(
            item => item.id === id
        );

        if (!producto) {
            return;
        }

        let cantidad = Number(nuevaCantidad);

        if (cantidad < 1) {
            cantidad = 1;
        }

        if (cantidad > producto.stock) {
            cantidad = producto.stock;

            mostrarMensaje(
                `El stock máximo disponible de ${producto.nombre} es ${producto.stock}.`
            );
        }

        producto.cantidad = cantidad;

        renderizarCarrito();
    }

    function eliminarProducto(id) {
        carrito = carrito.filter(
            item => item.id !== id
        );

        renderizarCarrito();
    }

    function renderizarCarrito() {
        if (carrito.length === 0) {
            detalleVenta.innerHTML = `
                <tr>
                    <td
                        colspan="5"
                        class="text-center py-5 text-muted"
                    >
                        Agrega productos a la venta.
                    </td>
                </tr>
            `;

            totalVenta.textContent = 'S/ 0.00';

            return;
        }

        detalleVenta.innerHTML = carrito.map(
            (producto, indice) => {
                const subtotal =
                    producto.precio * producto.cantidad;

                return `
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">
                                ${escapar(producto.nombre)}
                            </div>

                            <small class="text-muted">
                                ${escapar(producto.codigo_barras)}
                                — Stock: ${producto.stock}
                            </small>

                            <input
                                type="hidden"
                                name="productos[${indice}][producto_id]"
                                value="${producto.id}"
                            >
                        </td>

                        <td>
                            S/ ${producto.precio.toFixed(2)}
                        </td>

                        <td>
                            <input
                                type="number"
                                name="productos[${indice}][cantidad]"
                                class="form-control"
                                value="${producto.cantidad}"
                                min="1"
                                max="${producto.stock}"
                                onchange="cambiarCantidad(
                                    ${producto.id},
                                    this.value
                                )"
                                required
                            >
                        </td>

                        <td class="fw-semibold">
                            S/ ${subtotal.toFixed(2)}
                        </td>

                        <td class="text-end pe-4">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                onclick="eliminarProducto(${producto.id})"
                            >
                                Quitar
                            </button>
                        </td>
                    </tr>
                `;
            }
        ).join('');

        const total = carrito.reduce(
            (suma, producto) =>
                suma + producto.precio * producto.cantidad,
            0
        );

        totalVenta.textContent = `S/ ${total.toFixed(2)}`;
    }

    document
        .getElementById('botonCodigo')
        ?.addEventListener('click', agregarPorCodigo);

    document
        .getElementById('botonSeleccionar')
        ?.addEventListener('click', agregarSeleccionado);

    codigoBarras?.addEventListener('keydown', function (evento) {
        if (evento.key === 'Enter') {
            evento.preventDefault();
            agregarPorCodigo();
        }
    });

    document
        .getElementById('formVenta')
        ?.addEventListener('submit', function (evento) {
            if (carrito.length === 0) {
                evento.preventDefault();

                mostrarMensaje(
                    'Debes agregar al menos un producto.'
                );

                return;
            }

            if (!confirm('¿Deseas registrar esta venta?')) {
                evento.preventDefault();
            }
        });

    productosAnteriores.forEach(function (anterior) {
        const producto = catalogo.find(
            item => item.id === Number(anterior.producto_id)
        );

        if (producto) {
            agregarProducto(
                producto,
                Number(anterior.cantidad)
            );
        }
    });
</script>
@endpush