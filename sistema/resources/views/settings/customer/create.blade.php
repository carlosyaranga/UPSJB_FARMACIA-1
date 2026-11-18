@extends('template.administrador')

@section('title', 'CUSTOMERS')
@section('name_section', 'CLIENTES')
@section('description', 'Registrar cliente')
@section('item_section', 'Nuevo Registro')
@section('section', 'Clientes')
@section('module', 'Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('customer.store') }}" method="POST">
            @csrf
            @include('settings.customers.form')
        </form>
    </div>
</div>
@endsection
