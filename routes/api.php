<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
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


Route::post('/video', [VideoController::class, 'store'])->middleware('throttle:uploads')->withoutMiddleware('api');
Route::post('/meta', [VideoController::class, 'meta']);

Route::get('logout', [UserController::class, 'logout'])->middleware('auth:api');

Route::withoutMiddleware('auth:api')->group(function () {
    Route::post('login', [UserController::class, 'login']);
    Route::get('category', [CategoryController::class, 'index']);
    Route::get('tag', [TagController::class, 'index']);

    Route::prefix('video')->group(function () {
        Route::get('/start/{start}/count/{count}', [VideoController::class, 'index']);
        Route::get('/{video}', [VideoController::class, 'show_video']);
        Route::get('/{video}/comment', [CommentController::class, 'index']);
        Route::post('/search/start/{start}/count/{count}', [VideoController::class, 'search']);
        Route::get('/{video}/recommendation', [VideoController::class, 'recommendation']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/{user}/videos', [VideoController::class, 'show']);
        Route::get('/{user}/playlist', [PlaylistController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
    });
});


Route::middleware('role:user')->group(function () {
    Route::prefix('video')->group(function () {
        Route::post('/{video}/comment', [CommentController::class, 'store']);
        Route::get('/{video}/private', [VideoController::class, 'private']);
        Route::get('/{video}/public', [VideoController::class, 'public']);
        Route::get('/{video}/tag/{tag}', [VideoController::class, 'store_tag']);
        Route::delete('/{video}/tag/{tag}', [VideoController::class, 'destroy_tag']);
        Route::post('/{video}/update', [VideoController::class, 'update']);
        Route::delete('/{video}', [VideoController::class, 'destroy']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/{user}', [UserController::class, 'show']);
        Route::get('/video/{video}/playlist', [VideoController::class, 'video_playlists']);
        Route::get('/{user}/collections', [PlaylistController::class, 'collection']);
        Route::post('/{user}', [UserController::class, 'update']);
        Route::get('/{user}/follow', [SubscribeController::class, 'store']);
        Route::delete('/{user}/unfollow', [SubscribeController::class, 'destroy']);
    });

    Route::prefix('comment')->group(function () {
        Route::post('/{comment}/answer', [CommentController::class, 'store_answer']);
        Route::patch('/{comment}', [CommentController::class, 'update']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
    });

    Route::prefix('answer')->group(function () {
        Route::patch('/{answer}', [CommentController::class, 'update_answer']);
    });

    Route::prefix('playlist')->group(function () {
        Route::post('/', [PlaylistController::class, 'store']);
        Route::patch('/{playlist}', [PlaylistController::class, 'update']);
        Route::get('/{playlist}/collection', [PlaylistController::class, 'store_other']);
        Route::delete('/{playlist}/collection', [PlaylistController::class, 'destroy_other']);
        Route::get('/{playlist}/video/{video}', [PlaylistController::class, 'store_video']);
        Route::get('/{playlist}/public', [PlaylistController::class, 'public']);
        Route::get('/{playlist}/private', [PlaylistController::class, 'private']);
        Route::delete('/{playlist}/video/{video}', [PlaylistController::class, 'destroy_video']);
        Route::delete('/{playlist}', [PlaylistController::class, 'destroy']);
    });
});
