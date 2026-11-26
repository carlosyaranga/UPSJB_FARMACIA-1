@extends('template.administrador')

@section('title', 'Exportaciones Excel')
@section('name_section', 'Excel')
@section('description', 'Exportar e importar información')
@section('item_section', 'Reportes')
@section('route_section', route('reports.excel'))
@section('section', 'Excel')
@section('module', 'Principal')

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Exportaciones</strong></div><div class="card-body d-grid gap-2">
            <a class="btn btn-outline-success" href="{{ route('reports.medicines.export') }}"><i class="bi bi-file-earmark-excel me-1"></i>Lista de medicamentos e inventario</a>
            <a class="btn btn-outline-success" href="{{ route('reports.customers.export') }}"><i class="bi bi-file-earmark-excel me-1"></i>Lista de clientes</a>
            <form method="GET" action="{{ route('reports.sales.export') }}" class="row g-2"><div class="col"><input type="date" name="from" class="form-control"></div><div class="col"><input type="date" name="to" class="form-control"></div><div class="col-auto"><button class="btn btn-outline-success">Ventas</button></div></form>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Importar medicamentos</strong></div><div class="card-body">
            @include('partials.alerts')
            <form method="POST" action="{{ route('reports.medicines.import') }}" enctype="multipart/form-data">@csrf
                <label class="form-label">Archivo XLSX, XLS o CSV</label>
                <input type="file" name="file" class="form-control mb-2" accept=".xlsx,.xls,.csv" required>
                <small class="text-muted d-block mb-3">Encabezados requeridos: categoria, nombre, precio, stock, vencimiento.</small>
                <button class="btn btn-primary w-100">Importar</button>
            </form>
            @if (session('import_errors'))<div class="alert alert-warning mt-3"><strong>Errores:</strong><ul class="mb-0">@foreach(session('import_errors') as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        </div></div>
    </div>
</div>
@endsection
