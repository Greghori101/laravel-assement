<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(UserController::class)->prefix('/users')->middleware(['auth', 'verified'])->name('users.')->group(function () {
    Route::get('/',  'index')->name('index');
    Route::get('/create',  'create')->name('create');
    Route::post('/',  'store')->name('store');
    Route::get('/{user}',  'show')->name('show');
    Route::get('/{user}/edit',  'edit')->name('edit');
    Route::patch('/{user}',  'update')->name('update');
    Route::delete('/{user}',  'destroy')->name('destroy');
});

require __DIR__ . '/settings.php';
