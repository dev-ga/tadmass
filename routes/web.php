<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Como crear una migracion con los campos necesarion para un tabla que almacena informacion sobre productos y articulos medico quirurgicos en laravel 12 ?