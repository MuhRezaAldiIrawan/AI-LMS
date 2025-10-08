<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TipeKursusController;
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

Route::controller(CategoryController::class)->group(function(){
    Route::get('category', 'index')->name('category');
    Route::get('category/get-data', 'getKategori')->name('category.get-data');
    Route::get('category/create', 'create')->name('category.create');
    Route::post('category', 'store')->name('category.store');
    Route::get('category/{id}', 'edit')->name('category.edit');
    Route::post('category/{id}', 'update')->name('category.update');
    Route::delete('category/{id}', 'destroy')->name('category.destroy');

});


Route::controller(TipeKursusController::class)->group(function(){
    Route::get('tipekursus', 'index')->name('tipekursus');
});
