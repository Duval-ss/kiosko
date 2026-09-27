<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Kiosko Junior')
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">

            <a
                class="navbar-brand fw-bold"
                href="{{ route('categorias.index') }}"
            >
                Kiosko Junior
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal"
                aria-expanded="false"
                aria-label="Abrir menú"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div
                class="collapse navbar-collapse"
                id="menuPrincipal"
            >
                <div class="navbar-nav">

                    <a
                        class="nav-link
                            {{ request()->routeIs('categorias.*')
                                ? 'active'
                                : '' }}"
                        href="{{ route('categorias.index') }}"
                    >
                        Categorías
                    </a>

                    <a
                        class="nav-link
                            {{ request()->routeIs('productos.*')
                                ? 'active'
                                : '' }}"
                        href="{{ route('productos.index') }}"
                    >
                        Productos
                    </a>

                    <a
                        class="nav-link
                            {{ request()->routeIs('movimientos.*')
                                ? 'active'
                                : '' }}"
                        href="{{ route('movimientos.index') }}"
                    >
                        Inventario
                    </a>

                    <a
                        class="nav-link
                            {{ request()->routeIs('ventas.*')
                                ? 'active'
                                : '' }}"
                        href="{{ route('ventas.index') }}"
                    >
                        Ventas
                    </a>

                </div>
            </div>

        </div>
    </nav>

    <main class="container py-4">

        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >
                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >
                <strong>
                    No se pudo completar la operación.
                </strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        @yield('content')

    </main>

    <footer class="border-top bg-white py-3 mt-5">
        <div class="container text-center text-muted">
            <small>
                Sistema de Gestión de Inventario y Ventas
                — Kiosko Junior
            </small>
        </div>
    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>

    @stack('scripts')

</body>
</html>