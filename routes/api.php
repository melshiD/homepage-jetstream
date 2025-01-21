<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipesController;
use App\Http\Controllers\N8nController;

Route::post('/import', [RecipesController::class, 'import']);

Route::post('/n8n/trigger-workflow', [N8nController::class, 'triggerWorkflow']);
Route::post('/import-recipe', [N8nController::class, 'handleResearchResponse']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/testRecipeJson', [N8nController::class, 'testRecipeJson'])->name('testRecipeJson');

//webhooks to get env variable-assigned URLs
Route::get('/webhook-url', [N8nController::class, 'getWebhookUrl'])->name('webhook-url');

