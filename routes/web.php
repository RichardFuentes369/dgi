<?php

use App\Http\Controllers\Api\CrudControllerPrueba;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});