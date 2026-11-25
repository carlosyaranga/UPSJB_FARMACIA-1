@extends('template.administrador')
@section('title', 'Editar Medicamento')
@section('name_section', 'MEDICAMENTO')
@section('description', 'Editar Medicamento')
@section('item_section', 'Inicio')
@section('section', 'MEDICAMENTO')
@section('module', 'Gestión')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('medicamentos.update',$medicamento) }}">@csrf@method('PUT')@include('medicamentos.form')</form></div></div>@endsection