<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameServerController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::post('/locale', LocaleController::class)->name('locale.update');
Route::get('/servers', [ServerController::class, 'index'])->name('servers.index');
Route::get('/servers/{server:slug}', [ServerController::class, 'show'])->name('servers.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{post:slug}', [NewsController::class, 'show'])->name('news.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/admin/login', [SessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [SessionController::class, 'store'])->middleware('throttle:5,1')->name('admin.login.store');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('servers', GameServerController::class)->except('show');
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
