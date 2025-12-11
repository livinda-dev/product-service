<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProductController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/health', function () {
    return response()->json([
        'service' => 'product-service',
        'status' => 'OK',
        'time' => now(),
    ]);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('products', ProductController::class);
});
