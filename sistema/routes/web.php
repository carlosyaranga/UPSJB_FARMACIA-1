<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\customerController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index'); 

Route::resource('categories', CategoryController::class)->except('show');

Route::resource('roles', RoleController::class)->except('show');

Route::resource('customer', CustomerController::class)->except('show');