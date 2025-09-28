<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::resource('login', App\Http\Controllers\Auth\AuthController::class);
Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'store'])->name('login');
Route::resource('dashboard', App\Http\Controllers\DashboardController::class);
