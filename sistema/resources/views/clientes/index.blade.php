@extends('template.administrador')
@section('title', 'Clientes')
@section('name_section', 'CLIENTES')
@section('description', 'Administración de clientes')
@section('item_section', 'Inicio')
@section('section', 'CLIENTES')
@section('module', 'Gestión')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex flex-wrap justify-content-between gap-2 align-items-center">
<a href="{{ route('clientes.create') }}" class="btn btn-primary text-white">+ Nuevo registro</a><form method="GET" action="{{ route('clientes.index') }}" class="d-flex gap-2"><input name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar..."><button class="btn btn-outline-primary">Buscar</button></form></div><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>#</th><th>Nombre</th><th>DNI</th><th>Teléfono</th><th>Edad</th><th>Acciones</th></tr></thead><tbody>@forelse($clientes as $item)<tr><td>{{ $loop->iteration + ($clientes->currentPage()-1)*$clientes->perPage() }}</td><td>{{ $item->nombre }}</td><td>{{ $item->dni }}</td><td>{{ $item->telefono ?: '—' }}</td><td>{{ $item->edad ?: '—' }}</td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('clientes.edit',$item) }}">Editar</a>
<form class="d-inline" method="POST" action="{{ route('clientes.destroy',$item) }}" onsubmit="return confirm('¿Deseas eliminar este registro?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No hay registros.</td></tr>@endforelse</tbody></table></div>{{ $clientes->links() }}}}</div></div>
@endsection
