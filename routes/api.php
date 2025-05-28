<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [UserController::class, 'login']);
Route::get('logout', [UserController::class, 'logout']);

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{user}/playlist', [PlaylistController::class, 'show']);
    Route::get('/{user}/subscribers', [SubscribeController::class, 'index']);
    Route::get('/{user}/follow', [SubscribeController::class, 'store']);
    Route::post('/', [UserController::class, 'store']);
    Route::delete('/{user}/unfollow', [SubscribeController::class, 'destroy']);
});

Route::prefix('video')->group(function () {
    Route::get('/', [VideoController::class, 'index']);
    Route::get('/my', [VideoController::class, 'self_index']);
    Route::get('/{video}/private', [VideoController::class, 'private']);
    Route::get('/{video}/public', [VideoController::class, 'public']);
    Route::post('/{video}/tag/{tag}', [VideoController::class, 'tag']);
    Route::post('/', [VideoController::class, 'store']);
    Route::patch('/update', [VideoController::class, 'update']);
    Route::get('/{video}/comment', [CommentController::class, 'index']);
    Route::delete('/{video}', [VideoController::class, 'destroy']);
    Route::post('/{video}/report', [ReportController::class, 'store']);
});

Route::prefix('report')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::delete('/{report}/complete', [ReportController::class, 'destroy']);
});

Route::prefix('comment')->group(function () {
    Route::post('/{comment}/answer', [CommentController::class, 'store_answer']);
    Route::post('/', [CommentController::class, 'store']);
    Route::patch('/', [CommentController::class, 'update']);
    Route::delete('/{comment}', [CommentController::class, 'destroy']);
});

Route::prefix('playlist')->group(function () {
    Route::post('/', [PlaylistController::class, 'store']);
    Route::post('/{playlist}/video', [PlaylistController::class, 'store_video']);
    Route::get('/{playlist}/public', [PlaylistController::class, 'public']);
    Route::get('/{playlist}/private', [PlaylistController::class, 'private']);
    Route::delete('/{playlist}/video/{video}', [PlaylistController::class, 'destroy_video']);
    Route::delete('/{playlist}', [PlaylistController::class, 'destroy']);
});
