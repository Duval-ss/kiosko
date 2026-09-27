<div class="row">
    <div class="col-md-6 mb-3">
        <label for="categoria_id" class="form-label">
            Categoría
        </label>

        <select
            id="categoria_id"
            name="categoria_id"
            class="form-select @error('categoria_id') is-invalid @enderror"
            required
        >
            <option value="">Seleccione una categoría</option>

            @foreach ($categorias as $categoria)
                <option
                    value="{{ $categoria->id }}"
                    @selected(
                        old(
                            'categoria_id',
                            $producto->categoria_id ?? ''
                        ) == $categoria->id
                    )
                >
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>

        @error('categoria_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="codigo_barras" class="form-label">
            Código de barras
        </label>

        <input
            type="text"
            id="codigo_barras"
            name="codigo_barras"
            class="form-control @error('codigo_barras') is-invalid @enderror"
            value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}"
            maxlength="50"
            required
        >

        @error('codigo_barras')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="nombre" class="form-label">
        Nombre del producto
    </label>

    <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control @error('nombre') is-invalid @enderror"
        value="{{ old('nombre', $producto->nombre ?? '') }}"
        maxlength="150"
        required
        autofocus
    >

    @error('nombre')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="descripcion" class="form-label">
        Descripción
    </label>

    <textarea
        id="descripcion"
        name="descripcion"
        class="form-control @error('descripcion') is-invalid @enderror"
        rows="3"
        maxlength="500"
    >{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>

    @error('descripcion')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="precio_compra" class="form-label">
            Precio de compra
        </label>

        <div class="input-group">
            <span class="input-group-text">S/</span>

            <input
                type="number"
                id="precio_compra"
                name="precio_compra"
                class="form-control @error('precio_compra') is-invalid @enderror"
                value="{{ old('precio_compra', $producto->precio_compra ?? '') }}"
                min="0"
                step="0.01"
                required
            >

            @error('precio_compra')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <label for="precio_venta" class="form-label">
            Precio de venta
        </label>

        <div class="input-group">
            <span class="input-group-text">S/</span>

            <input
                type="number"
                id="precio_venta"
                name="precio_venta"
                class="form-control @error('precio_venta') is-invalid @enderror"
                value="{{ old('precio_venta', $producto->precio_venta ?? '') }}"
                min="0"
                step="0.01"
                required
            >

            @error('precio_venta')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="stock" class="form-label">
            Stock inicial
        </label>

        <input
            type="number"
            id="stock"
            name="stock"
            class="form-control @error('stock') is-invalid @enderror"
            value="{{ old('stock', $producto->stock ?? 0) }}"
            min="0"
            required
        >

        @error('stock')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="stock_minimo" class="form-label">
            Stock mínimo
        </label>

        <input
            type="number"
            id="stock_minimo"
            name="stock_minimo"
            class="form-control @error('stock_minimo') is-invalid @enderror"
            value="{{ old('stock_minimo', $producto->stock_minimo ?? 5) }}"
            min="0"
            required
        >

        @error('stock_minimo')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>

<div class="mb-4">
    <input type="hidden" name="estado" value="0">

    <div class="form-check form-switch">
        <input
            type="checkbox"
            id="estado"
            name="estado"
            class="form-check-input"
            value="1"
            @checked(
                old(
                    'estado',
                    isset($producto) ? $producto->estado : true
                )
            )
        >

        <label for="estado" class="form-check-label">
            Producto activo
        </label>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-success">
        Guardar
    </button>

    <a
        href="{{ route('productos.index') }}"
        class="btn btn-secondary"
    >
        Cancelar
    </a>
</div>