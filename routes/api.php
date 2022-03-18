<?php

use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\ApiAuth\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('posts', PostController::class)
            ->except([
                'index', 'show'
            ]);
        Route::apiResource('comments', CommentController::class)
            ->except([
                'show'
            ]);
    });

    Route::get('posts/{post}', [PostController::class, 'show']);
    Route::get('posts', [PostController::class, 'index']);

    Route::get('comments/{comment}', [CommentController::class, 'show']);
});
