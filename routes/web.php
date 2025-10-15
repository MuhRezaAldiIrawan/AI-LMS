<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseTypeController;
use App\Http\Controllers\Course\ModuleController;
use App\Http\Controllers\Course\LessonController;
use App\Http\Controllers\Course\QuizController;
use App\Http\Controllers\Course\QuestionController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RedeemtionController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AiAssistantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index']);

Route::controller(AuthController::class)->group(function(){
    Route::get('login', 'index');
    Route::post('login', 'store')->name('login');
    Route::post('logout', 'logout')->name('logout');
});


Route::middleware('auth')->group(function () {

    Route::resource('dashboard', App\Http\Controllers\DashboardController::class);

    // Admin only routes - hanya admin yang bisa akses
    Route::middleware('admin')->group(function () {
        // User Management
        Route::controller(UsersController::class)->group(function(){
            Route::get('users', 'index')->name('users');
            Route::get('users/get-data', 'getUsers')->name('users.get-data');
            Route::get('users/create', 'create')->name('users.create');
            Route::post('users', 'store')->name('users.store');
            Route::get('users/{id}', 'edit')->name('users.edit');
            Route::post('users/{id}', 'update')->name('users.update');
            Route::delete('users/{id}', 'destroy')->name('users.destroy');
        });

        // Category Management
        Route::controller(CategoryController::class)->group(function(){
            Route::get('category', 'index')->name('category');
            Route::get('category/get-data', 'getKategori')->name('category.get-data');
            Route::get('category/create', 'create')->name('category.create');
            Route::post('category', 'store')->name('category.store');
            Route::get('category/{id}', 'edit')->name('category.edit');
            Route::post('category/{id}', 'update')->name('category.update');
            Route::delete('category/{id}', 'destroy')->name('category.destroy');
        });

        // Course Type Management
        Route::controller(CourseTypeController::class)->group(function(){
            Route::get('coursetype', 'index')->name('coursetype');
            Route::get('coursetype/get-data', 'getCourseType')->name('coursetype.get-data');
            Route::get('coursetype/create', 'create')->name('coursetype.create');
            Route::post('coursetype', 'store')->name('coursetype.store');
            Route::get('coursetype/{id}', 'edit')->name('coursetype.edit');
            Route::post('coursetype/{id}', 'update')->name('coursetype.update');
            Route::delete('coursetype/{id}', 'destroy')->name('coursetype.destroy');
        });

        // Location Management
        Route::controller(LocationController::class)->group(function(){
            Route::get('location', 'index')->name('location');
            Route::get('location/get-data', 'getLocation')->name('location.get-data');
            Route::get('location/create', 'create')->name('location.create');
            Route::post('location', 'store')->name('location.store');
            Route::get('location/{id}', 'edit')->name('location.edit');
            Route::post('location/{id}', 'update')->name('location.update');
            Route::delete('location/{id}', 'destroy')->name('location.destroy');
        });

        // Rewards Management
        Route::controller(RewardsController::class)->group(function(){
            Route::get('rewards', 'index')->name('rewards');
            Route::get('rewards/get-data', 'getRewards')->name('rewards.get-data');
            Route::get('rewards/create', 'create')->name('rewards.create');
            Route::post('rewards', 'store')->name('rewards.store');
            Route::get('rewards/{id}', 'edit')->name('rewards.edit');
            Route::post('rewards/{id}', 'update')->name('rewards.update');
            Route::delete('rewards/{id}', 'destroy')->name('rewards.destroy');
        });
    });

    Route::middleware('role:admin,karyawan')->group(function () {
        Route::controller(RedeemtionController::class)->group(function(){
            Route::get('redeemtion', 'index')->name('redeemtion');
            Route::get('redeemtion/get-data', 'getRedeemData')->name('redeemtion.getData');
        });
    });

    Route::controller(CourseController::class)->group(function(){
        Route::get('course', 'index')->name('course');

        Route::middleware('role:admin,pengajar')->group(function() {
            Route::get('course/create', 'create')->name('course.create');
            Route::post('course', 'store')->name('course.store');
            Route::post('course/{id}', 'update')->name('course.update');
            Route::post('course/{course}/update-participants', 'updateParticipants')->name('course.update-participants');
            Route::put('course/publish/{id}', 'update')->name('course.publish.update');
        });

        Route::middleware('role:karyawan,pengajar')->post('course/{course}/enroll', 'enroll')->name('course.enroll');

        Route::middleware('course.access')->get('course/{id}', 'show')->name('course.show');
    });

    Route::middleware('role:admin,pengajar')->controller(ModuleController::class)->group(function(){
        Route::post('module', 'store')->name('module.store');
        Route::put('module/{id}', 'update')->name('module.update');
        Route::delete('module/{id}', 'destroy')->name('module.destroy');
    });

    Route::middleware('role:admin,pengajar')->controller(LessonController::class)->group(function(){
        Route::post('lesson', 'store')->name('lesson.store');
        Route::get('lesson/{id}/edit', 'edit')->name('lesson.edit');
        Route::put('lesson/{id}', 'update')->name('lesson.update');
        Route::delete('lesson/{id}', 'destroy')->name('lesson.destroy');
    });

    Route::controller(LessonController::class)->group(function(){
        Route::middleware('course.access')->get('lesson/{id}', 'show')->name('lesson.show');
        Route::middleware('course.access')->post('lesson/{id}/complete', 'complete')->name('lesson.complete');
    });

    Route::middleware('role:admin,pengajar')->controller(QuizController::class)->group(function(){
        Route::post('quiz', 'store')->name('quiz.store');
        Route::get('quiz/{id}/edit', 'edit')->name('quiz.edit');
        Route::put('quiz/{id}', 'update')->name('quiz.update');
        Route::delete('quiz/{id}', 'destroy')->name('quiz.destroy');
        Route::get('quiz/{id}/manage', 'manage')->name('quiz.manage');
    });


    Route::controller(QuizController::class)->group(function(){
        Route::middleware('course.access')->get('quiz/{id}', 'show')->name('quiz.show');
        Route::middleware('course.access')->get('quiz/{id}/attempt', 'attempt')->name('quiz.attempt');
        Route::middleware('course.access')->post('quiz/{quizId}/attempt/{attemptId}/submit', 'submit')->name('quiz.submit');
        Route::middleware('course.access')->get('quiz/attempt/{attemptId}/review', 'reviewAttempt')->name('quiz.attempt.review');
    });


    Route::middleware('role:admin,pengajar')->controller(QuestionController::class)->group(function(){
        Route::post('question', 'store')->name('question.store');
        Route::put('question/{id}', 'update')->name('question.update');
        Route::delete('question/{id}', 'destroy')->name('question.destroy');
    });


    Route::middleware('role:admin,karyawan,pengajar')->controller(AiAssistantController::class)->group(function(){
        Route::get('aiassistant', 'index')->name('aiassistant');
        Route::post('aiassistant/ask', 'ask')->name('aiassistant.ask');
        Route::get('aiassistant/history', 'getHistory')->name('aiassistant.history');
        Route::delete('aiassistant/clear', 'clearHistory')->name('aiassistant.clear');
        Route::post('aiassistant/new-session', 'newSession')->name('aiassistant.new-session');
        Route::post('aiassistant/lesson-chat', 'lessonChat')->name('aiassistant.lesson-chat');
    });

    Route::controller(UsersController::class)->group(function(){
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile/{id}', 'updateProfile')->name('users.profile.update');

    });

    // Certificate Routes
    Route::controller(App\Http\Controllers\CertificateController::class)->group(function(){
        // Download certificate (user yang memiliki atau admin)
        Route::get('certificate/{id}/download', 'download')->name('certificate.download');

        // Preview certificate (user yang memiliki atau admin)
        Route::get('certificate/{id}/preview', 'preview')->name('certificate.preview');

        // Get certificate for course (AJAX)
        Route::get('certificate/course/{courseId}', 'getCertificateForCourse')->name('certificate.for-course');

        // Admin only routes
        Route::middleware('admin')->group(function () {
            Route::post('certificate/generate', 'generate')->name('certificate.generate');
            Route::post('certificate/{id}/regenerate', 'regenerate')->name('certificate.regenerate');
        });
    });

    // Public certificate verification (no auth required, moved outside auth middleware)
});

// Public certificate verification route (accessible without login)
Route::get('certificate/verify', [App\Http\Controllers\CertificateController::class, 'verify'])->name('certificate.verify');


