<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MangoDiseaseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [MangoDiseaseController::class, 'index'])->name('home');
