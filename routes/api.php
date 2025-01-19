<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipesController;
use App\Http\Controllers\N8nController;

Route::post('/import', [RecipesController::class, 'import']);

Route::post('/n8n/trigger-workflow', [N8nController::class, 'triggerWorkflow']);
Route::post('/n8n/handle-response', [N8nController::class, 'handleWebhook']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



//webhooks to get env variable-assigned URLs
Route::get('/webhook-url', [RecipesController::class, 'getWebhookUrl'])->name('webhook-url');

