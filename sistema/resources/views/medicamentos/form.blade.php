
<div class="mb-3">
    <label class="form-label">Nombre *</label>
    <input type="text"
           name="nombre"
           class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $medicamento->nombre ?? '') }}"
           required>

    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Categoría *</label>
    <select name="categoria_id"
            class="form-select @error('categoria_id') is-invalid @enderror"
            required>
        <option value="">Seleccione...</option>

        @foreach($categorias as $c)
            <option value="{{ $c->id }}"
                @selected(old('categoria_id', $medicamento->categoria_id ?? '') == $c->id)>
                {{ $c->nombre }}
            </option>
        @endforeach
    </select>

    @error('categoria_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Precio (S/) *</label>
        <input type="number"
               step="0.01"
               min="0.01"
               name="precio"
               class="form-control"
               value="{{ old('precio', $medicamento->precio ?? '') }}"
               required>

        @error('precio')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Stock *</label>
        <input type="number"
               min="0"
               name="stock"
               class="form-control"
               value="{{ old('stock', $medicamento->stock ?? 0) }}"
               required>

        @error('stock')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<button type="submit" class="btn btn-primary text-white">
    Guardar
</button>

<a href="{{ route('medicamentos.index') }}" class="btn btn-secondary">
    Cancelar
</a>
