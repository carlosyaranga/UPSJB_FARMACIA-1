{{-- Hereda la plantilla principal y define los datos de navegación de Gestión. --}}
@extends('template.administrador')

@section('title', 'Proveedores')
@section('name_section', 'Proveedores')
@section('description', 'Listado de proveedores')
@section('item_section', 'Inicio')
@section('route_section', route('suppliers.index'))
@section('section', 'Proveedores')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-header bg-white">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white">
            <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
        </a>
    </div>
    <div class="card-body">
        @include('partials.alerts')

        <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $suppliers->count() }}</h6>
        <table id="display" class="table table-sm table-hover w-100 bg-white">
            <thead>
                <tr>
                    <th class="text-center align-middle text-uppercase small">Nº</th>
                    <th class="text-center align-middle text-uppercase small">Razón social</th>
                    <th class="text-center align-middle text-uppercase small">RUC</th>
                    <th class="text-center align-middle text-uppercase small">Teléfono</th>
                    <th class="text-center align-middle text-uppercase small">Correo</th>
                    <th class="text-center align-middle text-uppercase small">Dirección</th>
                    <th class="text-center align-middle text-uppercase small">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle">{{ $supplier->name }}</td>
                        <td class="text-center align-middle">{{ $supplier->ruc }}</td>
                        <td class="text-center align-middle">{{ $supplier->phone ?: '—' }}</td>
                        <td class="align-middle">{{ $supplier->email ?: '—' }}</td>
                        <td class="align-middle">{{ $supplier->address ?: '—' }}</td>
                        <td class="text-center align-middle">
                            <div class="dropdown">
                                <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('suppliers.edit', $supplier) }}">Editar</a></li>
                                    <li>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                            onsubmit="return confirm('¿Seguro que desea eliminar este proveedor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">No se encontraron proveedores</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
