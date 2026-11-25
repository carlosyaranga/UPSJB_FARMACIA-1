@extends('template.administrador')
@section('title', 'Nuevo Cliente')
@section('name_section', 'CLIENTE')
@section('description', 'Nuevo Cliente')
@section('item_section', 'Inicio')
@section('section', 'CLIENTE')
@section('module', 'Gestión')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('clientes.store') }}">@csrf@include('clientes.form')</form></div></div>@endsection