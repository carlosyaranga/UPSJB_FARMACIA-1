@extends('template.administrador')
@section('title', 'Medicamentos')
@section('name_section', 'MEDICAMENTOS')
@section('description', 'Administración de medicamentos')
@section('item_section', 'Inicio')
@section('section', 'MEDICAMENTOS')
@section('module', 'Gestión')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex flex-wrap justify-content-between gap-2 align-items-center">
<a href="{{ route('medicamentos.create') }}" class="btn btn-primary text-white">+ Nuevo registro</a><form method="GET" action="{{ route('medicamentos.index') }}" class="d-flex gap-2"><input name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar..."><button class="btn btn-outline-primary">Buscar</button></form></div><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>#</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead><tbody>@forelse($medicamentos as $item)<tr><td>{{ $loop->iteration + ($medicamentos->currentPage()-1)*$medicamentos->perPage() }}</td><td>{{ $item->nombre }}</td><td>{{ $item->categoria?->nombre }}</td><td>S/ {{ number_format($item->precio,2) }}</td><td><span class="badge {{ $item->stock < 5 ? 'bg-danger' : 'bg-success' }}">{{ $item->stock }}</span></td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('medicamentos.edit',$item) }}">Editar</a>
<form class="d-inline" method="POST" action="{{ route('medicamentos.destroy',$item) }}" onsubmit="return confirm('¿Deseas eliminar este registro?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No hay registros.</td></tr>@endforelse</tbody></table></div>{{ $medicamentos->links() }}}}</div></div>
@endsection
