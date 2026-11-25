@extends('layouts.app')

@section('content')

<h3>Registrar medicamento</h3>

<form action="{{ route('medicamentos.store') }}" method="POST">
    @csrf
    @include('medicamentos.form')
</form>

@endsection