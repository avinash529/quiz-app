<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:8,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [QuizController::class, 'home'])->name('home');
    Route::post('/start-quiz', [QuizController::class, 'startQuiz'])->name('quiz.start');
    Route::get('/quiz', [QuizController::class, 'quiz'])->name('quiz');
    Route::post('/answer', [QuizController::class, 'answer'])->name('quiz.answer');
    Route::get('/result', [QuizController::class, 'result'])->name('result');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
