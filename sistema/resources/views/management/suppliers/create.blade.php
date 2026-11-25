{{-- Hereda la plantilla principal y define los datos de navegación de Gestión. --}}
@extends('template.administrador')

@section('title', 'Proveedores')
@section('name_section', 'Proveedores')
@section('description', 'Nuevo registro de proveedor')
@section('item_section', 'Nuevo')
@section('route_section', route('suppliers.index'))
@section('section', 'Proveedores')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            @include('management.suppliers.form')
        </form>
    </div>
</div>
@endsection
