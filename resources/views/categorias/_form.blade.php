<div class="mb-3">
    <label
        for="nombre"
        class="form-label"
    >
        Nombre de la categoría
    </label>

    <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control @error('nombre') is-invalid @enderror"
        value="{{ old('nombre', $categoria->nombre ?? '') }}"
        maxlength="100"
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
    <label
        for="descripcion"
        class="form-label"
    >
        Descripción
    </label>

    <textarea
        id="descripcion"
        name="descripcion"
        class="form-control @error('descripcion') is-invalid @enderror"
        rows="4"
        maxlength="500"
    >{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>

    @error('descripcion')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-4">
    <input
        type="hidden"
        name="estado"
        value="0"
    >

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
                    isset($categoria) ? $categoria->estado : true
                )
            )
        >

        <label
            for="estado"
            class="form-check-label"
        >
            Categoría activa
        </label>
    </div>
</div>

<div class="d-flex gap-2">
    <button
        type="submit"
        class="btn btn-success"
    >
        Guardar
    </button>

    <a
        href="{{ route('categorias.index') }}"
        class="btn btn-secondary"
    >
        Cancelar
    </a>
</div>