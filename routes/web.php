<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RecipesController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/privacy', function () {
        return Inertia::render('PrivacyPolicy');
    })->name('privacy');

    Route::get('/flow', function () {
        return Inertia::render('Flow');
    })->name('flow');

    Route::get('/build-db', function () {
        return Inertia::render('Build');
    })->name('build-db');
    Route::post('/recipes/import', [RecipeController::class, 'import']);



    Route::post('/recipes', [RecipesController::class, 'store'])->name('recipes');
});
