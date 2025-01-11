<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

    Route::post('/submit-recipe-json', function (Request $request) {
        // Get JSON data from the request
        $jsonString = $request->input('jsonData');

        // Decode the JSON string into a PHP array
        $data = json_decode($jsonString, true);

        // Debug the data to see the structure
        dd($data);
        // Pass the data to the 'receipt' view
        return view('receipe', ['data' => $data]);
    })->name('submit-recipe-json');
});
