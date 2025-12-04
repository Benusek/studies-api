<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('uploads')->group(function () {
    Route::get("/playlist/{folder}/{filename}", [VideoController::class, 'getVideo'])->name("video.playlist");
    Route::get("/file/{folder}/{filename}", [VideoController::class, 'getFile'])->name("video.file");
});

Route::get('/about', function () {
    return 'This is the About Us page.';
});

Route::get('/users/{id}', function ($id) {
    return 'User ID: ' . $id;
});
