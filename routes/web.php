<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KategoriController;
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
    Route::get('users/{id}', 'edit')->name('users.edit');
    Route::post('users/{id}', 'update')->name('users.update');
    Route::delete('users/{id}', 'destroy')->name('users.destroy');
});

Route::controller(KategoriController::class)->group(function(){
    Route::get('kategori', 'index')->name('kategori');
    Route::get('kategori/get-data', 'getKategori')->name('kategori.get-data');
    Route::get('kategori/create', 'create')->name('kategori.create');
    Route::post('kategori', 'store')->name('kategori.store');
    Route::get('kategori/{id}', 'edit')->name('kategori.edit');
    Route::post('kategori/{id}', 'update')->name('kategori.update');
    Route::delete('kategori/{id}', 'destroy')->name('kategori.destroy');

});
