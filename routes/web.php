<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::resource('login', App\Http\Controllers\Auth\AuthController::class);
Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'store'])->name('login');
Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');
Route::resource('dashboard', App\Http\Controllers\DashboardController::class);
