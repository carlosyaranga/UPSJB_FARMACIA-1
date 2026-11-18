<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="nombre" class="form-label text-uppercase small fw-medium">Nombre Completo <span class="text-danger">*</span></label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $customer->nombre ?? '') }}"
            class="form-control @error('nombre') is-invalid @enderror" placeholder="Ingrese el nombre del cliente" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="dni" class="form-label text-uppercase small fw-medium">DNI <span class="text-danger">*</span></label>
        <input type="text" id="dni" name="dni" value="{{ old('dni', $customer->dni ?? '') }}"
            class="form-control @error('dni') is-invalid @enderror" placeholder="Ingrese el DNI" required>
        @error('dni')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="telefono" class="form-label text-uppercase small fw-medium">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $customer->telefono ?? '') }}"
            class="form-control @error('telefono') is-invalid @enderror" placeholder="Ingrese el teléfono">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="edad" class="form-label text-uppercase small fw-medium">Edad</label>
        <input type="number" id="edad" name="edad" value="{{ old('edad', $customer->edad ?? '') }}"
            class="form-control @error('edad') is-invalid @enderror" placeholder="Ingrese la edad">
        @error('edad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('customer.index') }}" class="btn btn-secondary rounded-3 text-uppercase small">Cancelar</a>
        <button type="submit" class="btn btn-primary rounded-3 text-uppercase text-white small">Guardar</button>
    </div>
</div>
