<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

//Para forzar la redireccion al login de filament
Route::redirect('/', '/admin');