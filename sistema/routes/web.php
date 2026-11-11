<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('template/administrador');
});

use App\Models\Medicamento;

Route::get('/medicamentos', function () {
    return Medicamento::with('categoria')->get();
});

Route::get('/medicamentos/{id}', function ($id) {
    return Medicamento::with('categoria')->findOrFail($id);
});

Route::resource('categories', CategoryController::class)->except('show');
