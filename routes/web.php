<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::resource('dashboard', App\Http\Controllers\DashboardController::class);

Route::controller(AuthController::class)->group(function(){
    Route::get('login', 'index');
    Route::post('login', 'store')->name('login');
    Route::post('logout', 'logout')->name('logout');
});


Route::controller(UsersController::class)->group(function(){
    Route::get('users', 'index')->name('users');
    Route::get('users/get-data', 'getUsers')->name('users.get-data');
    Route::get('users/create', 'create')->name('users.create');
    Route::post('users', 'store')->name('users.store');
    // Route::get('users/{id}/edit', 'edit')->name('users.edit');
    // Route::put('users/{id}', 'update')->name('users.update');
    // Route::delete('users/{id}', 'destroy')->name('users.destroy');
});
