{{-- Formulario compartido: old() conserva los datos y el modelo llena el formulario al editar. --}}
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label for="name" class="form-label">Razón social</label>
        <input type="text" name="name" id="name" maxlength="100"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $supplier->name ?? '') }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="ruc" class="form-label">RUC</label>
        <input type="text" name="ruc" id="ruc" maxlength="11" inputmode="numeric" pattern="[0-9]{11}"
            class="form-control @error('ruc') is-invalid @enderror"
            value="{{ old('ruc', $supplier->ruc ?? '') }}">
        @error('ruc')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="phone" class="form-label">Teléfono <small class="text-muted">(opcional)</small></label>
        <input type="text" name="phone" id="phone" maxlength="9" inputmode="numeric" pattern="[0-9]{9}"
            class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $supplier->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="email" class="form-label">Correo electrónico <small class="text-muted">(opcional)</small></label>
        <input type="email" name="email" id="email" maxlength="100"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $supplier->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="address" class="form-label">Dirección <small class="text-muted">(opcional)</small></label>
        <input type="text" name="address" id="address" maxlength="150"
            class="form-control @error('address') is-invalid @enderror"
            value="{{ old('address', $supplier->address ?? '') }}">
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
