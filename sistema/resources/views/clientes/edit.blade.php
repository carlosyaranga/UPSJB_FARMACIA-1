@extends('template.administrador')
@section('title', 'Editar Cliente')
@section('name_section', 'CLIENTE')
@section('description', 'Editar Cliente')
@section('item_section', 'Inicio')
@section('section', 'CLIENTE')
@section('module', 'Gestión')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('clientes.update',$cliente) }}">@csrf@method('PUT')@include('clientes.form')</form></div></div>@endsection