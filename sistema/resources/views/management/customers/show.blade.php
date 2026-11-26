@extends('template.administrador')

@section('title', 'Historial del cliente')
@section('name_section', 'Historial del cliente')
@section('description', 'Compras registradas')
@section('item_section', 'Clientes')
@section('route_section', route('customers.index'))
@section('section', 'Historial')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><small class="text-muted d-block">Nombre</small><strong>{{ $customer->name }}</strong></div>
            <div class="col-md-3"><small class="text-muted d-block">DNI</small><strong>{{ $customer->dni }}</strong></div>
            <div class="col-md-3"><small class="text-muted d-block">Teléfono</small><strong>{{ $customer->phone ?? '—' }}</strong></div>
            <div class="col-md-2"><small class="text-muted d-block">Compras</small><strong>{{ $customer->sales->count() }}</strong></div>
        </div>
    </div>
</div>

<div class="card bg-white border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive"><table class="table table-sm table-hover mb-0">
            <thead><tr><th class="ps-3 text-uppercase small">Venta</th><th class="text-uppercase small">Fecha</th><th class="text-uppercase small">Vendedor</th><th class="text-end text-uppercase small">Total</th><th class="text-center text-uppercase small pe-3">Detalle</th></tr></thead>
            <tbody>
                @forelse ($customer->sales as $sale)
                    <tr>
                        <td class="ps-3">#{{ $sale->id }}</td>
                        <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user?->name }}</td>
                        <td class="text-end fw-semibold">S/ {{ number_format($sale->total, 2) }}</td>
                        <td class="text-center pe-3"><a class="btn btn-sm btn-outline-primary" href="{{ route('sales.show', $sale) }}">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Este cliente todavía no tiene compras.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
