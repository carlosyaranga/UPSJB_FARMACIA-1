@extends('template.administrador')

@section('title', 'Reportes')
@section('name_section', 'Reportes')
@section('description', 'Consultas y documentos de ventas')
@section('item_section', 'Inicio')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-2">
            <div class="col-md-3"><label class="form-label">Desde</label><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
            <div class="col-md-3"><label class="form-label">Hasta</label><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
            <div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary w-100">Consultar</button></div>
            <div class="col-md-3 d-flex align-items-end"><a class="btn btn-outline-danger w-100" href="{{ route('reports.sales.pdf', request()->only('from','to')) }}"><i class="bi bi-file-earmark-pdf me-1"></i>PDF ventas</a></div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><a class="card border-0 shadow-sm text-decoration-none h-100" href="{{ route('reports.inventory.pdf') }}"><div class="card-body"><i class="bi bi-box-seam fs-4 text-primary"></i><strong class="d-block mt-2">Inventario</strong></div></a></div>
    <div class="col-md-3"><a class="card border-0 shadow-sm text-decoration-none h-100" href="{{ route('reports.low-stock.pdf') }}"><div class="card-body"><i class="bi bi-exclamation-triangle fs-4 text-danger"></i><strong class="d-block mt-2">Stock bajo</strong></div></a></div>
    <div class="col-md-3"><a class="card border-0 shadow-sm text-decoration-none h-100" href="{{ route('reports.expiry.pdf') }}"><div class="card-body"><i class="bi bi-calendar-x fs-4 text-warning"></i><strong class="d-block mt-2">Vencimientos</strong></div></a></div>
    <div class="col-md-3"><a class="card border-0 shadow-sm text-decoration-none h-100" href="{{ route('reports.customers.pdf') }}"><div class="card-body"><i class="bi bi-people fs-4 text-success"></i><strong class="d-block mt-2">Clientes</strong></div></a></div>
</div>

<div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between"><strong>Historial de ventas</strong><span class="fw-bold">Total: S/ {{ number_format($total, 2) }}</span></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead><tr><th class="ps-3">Fecha</th><th>Cliente</th><th>Vendedor</th><th class="text-end pe-3">Total</th></tr></thead><tbody>@forelse($sales as $sale)<tr><td class="ps-3">{{ $sale->date->format('d/m/Y H:i') }}</td><td>{{ $sale->customer?->name }}</td><td>{{ $sale->user?->name }}</td><td class="text-end pe-3 fw-semibold">S/ {{ number_format($sale->total, 2) }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">No hay ventas en el período seleccionado.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection
