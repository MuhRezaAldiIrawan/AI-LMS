<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseTypeController;
use App\Http\Controllers\Course\ModuleController;
use App\Http\Controllers\Course\LessonController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RedeemtionController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\UsersController;
use App\Models\Module;
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


Route::controller(CourseTypeController::class)->group(function(){
    Route::get('coursetype', 'index')->name('coursetype');
    Route::get('coursetype/get-data', 'getCourseType')->name('coursetype.get-data');
    Route::get('coursetype/create', 'create')->name('coursetype.create');
    Route::post('coursetype', 'store')->name('coursetype.store');
    Route::get('coursetype/{id}', 'edit')->name('coursetype.edit');
    Route::post('coursetype/{id}', 'update')->name('coursetype.update');
    Route::delete('coursetype/{id}', 'destroy')->name('coursetype.destroy');
});

Route::controller(LocationController::class)->group(function(){
    Route::get('location', 'index')->name('location');
    Route::get('location/get-data', 'getLocation')->name('location.get-data');
    Route::get('location/create', 'create')->name('location.create');
    Route::post('location', 'store')->name('location.store');
    Route::get('location/{id}', 'edit')->name('location.edit');
    Route::post('location/{id}', 'update')->name('location.update');
    Route::delete('location/{id}', 'destroy')->name('location.destroy');
});

Route::controller(RewardsController::class)->group(function(){
    Route::get('rewards', 'index')->name('rewards');
    Route::get('rewards/get-data', 'getRewards')->name('rewards.get-data');
    Route::get('rewards/create', 'create')->name('rewards.create');
    Route::post('rewards', 'store')->name('rewards.store');
    Route::get('rewards/{id}', 'edit')->name('rewards.edit');
    Route::post('rewards/{id}', 'update')->name('rewards.update');
    Route::delete('rewards/{id}', 'destroy')->name('rewards.destroy');
});

Route::controller(RedeemtionController::class)->group(function(){
    Route::get('redeemtion', 'index')->name('redeemtion');
    Route::get('redeemtion/get-data', 'getRedeemData')->name('redeemtion.getData');
});

Route::controller(CourseController::class)->group(function(){
    Route::get('course', 'index')->name('course');
    Route::get('course/create', 'create')->name('course.create');
    Route::post('course', 'store')->name('course.store');
    Route::get('course/{id}', 'show')->name('course.show');
    Route::post('course/{id}', 'update')->name('course.update');
});

Route::controller(ModuleController::class)->group(function(){
    Route::post('module', 'store')->name('module.store');
    Route::put('module/{id}', 'update')->name('module.update');
    Route::delete('module/{id}', 'destroy')->name('module.destroy');
});

Route::controller(LessonController::class)->group(function(){
    Route::post('lesson', 'store')->name('lesson.store');
    Route::get('lesson/{id}', 'show')->name('lesson.show');
    Route::put('lesson/{id}', 'update')->name('lesson.update');
    Route::delete('lesson/{id}', 'destroy')->name('lesson.destroy');
});


