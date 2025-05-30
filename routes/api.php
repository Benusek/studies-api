<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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

Route::post('login', [UserController::class, 'login'])->withoutMiddleware('auth:api');
Route::get('logout', [UserController::class, 'logout'])->middleware('auth:api');

Route::prefix('video')->withoutMiddleware('auth:api')->group(function () {
    Route::get('/', [VideoController::class, 'index']);
    Route::get('/{video}/comment', [CommentController::class, 'index']);
});

Route::prefix('user')->withoutMiddleware('auth:api')->group(function () {
    Route::get('/{user}/videos', [VideoController::class, 'show']);
    Route::get('/{user}/playlist', [PlaylistController::class, 'show']);
    Route::get('/{user}/subscribers', [SubscribeController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
});

Route::middleware('role:user')->group(function () {
    Route::prefix('video')->group(function () {
        Route::get('/{video}/private', [VideoController::class, 'private']);
        Route::get('/{video}/public', [VideoController::class, 'public']);
        Route::get('/{video}/tag/{tag}', [VideoController::class, 'store_tag']);
        Route::delete('/{video}/tag/{tag}', [VideoController::class, 'destroy_tag']);
        Route::post('/', [VideoController::class, 'store']);
        Route::patch('/{video}/update', [VideoController::class, 'update']);
        Route::delete('/{video}', [VideoController::class, 'destroy']);
        Route::post('/{video}/report', [ReportController::class, 'store']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/{user}/follow', [SubscribeController::class, 'store']);
        Route::delete('/{user}/unfollow', [SubscribeController::class, 'destroy']);
    });

    Route::prefix('comment')->group(function () {
        Route::post('/{comment}/answer', [CommentController::class, 'store_answer']);
        Route::post('/', [CommentController::class, 'store']);
        Route::patch('/', [CommentController::class, 'update']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
    });

    Route::prefix('playlist')->group(function () {
        Route::post('/', [PlaylistController::class, 'store']);
        Route::get('/{playlist}/video/{video}', [PlaylistController::class, 'store_video']);
        Route::get('/{playlist}/public', [PlaylistController::class, 'public']);
        Route::get('/{playlist}/private', [PlaylistController::class, 'private']);
        Route::delete('/{playlist}/video/{video}', [PlaylistController::class, 'destroy_video']);
        Route::delete('/{playlist}', [PlaylistController::class, 'destroy']);
    });
});
    //->middleware('verified');

Route::middleware('role:moderator')->group(function () {
    Route::prefix('report')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::delete('/{report}/complete', [ReportController::class, 'destroy']);
    });
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
