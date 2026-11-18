<?php

namespace App\Http\Controllers;

use App\Models\Rol;                                                                                                                                               
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Rol::all();
        return view('settings.roles.index', compact('roles'));
    }

}