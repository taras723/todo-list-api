<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/tasks', [TaskWebController::class, 'index'])->name('web.tasks.index');
    Route::get('/tasks/create', [TaskWebController::class, 'create'])->name('web.tasks.create');
    Route::post('/tasks', [TaskWebController::class, 'store'])->name('web.tasks.store');
    Route::get('/tasks/{task}/edit', [TaskWebController::class, 'edit'])->name('web.tasks.edit');
    Route::put('/tasks/{task}', [TaskWebController::class, 'update'])->name('web.tasks.update');
    Route::delete('/tasks/{task}', [TaskWebController::class, 'destroy'])->name('web.tasks.destroy');
    Route::patch('/tasks/{task}/complete', [TaskWebController::class, 'complete'])->name('web.tasks.complete');
    Route::get('/auth', function () {
        return view('auth.profile', ['user' => auth()->user()]);
    })->name('auth.profile');
});