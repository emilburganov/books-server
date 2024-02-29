<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Auth guarded routes
Route::middleware('auth.token')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/books/selected', [BookController::class, 'getSelected']);
    Route::patch('/books/unselect/all', [BookController::class, 'unselectAll']);

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('genres', GenreController::class);
        Route::apiResource('authors', AuthorController::class);
        Route::apiResource('books', BookController::class)->except('index', 'show');
        Route::apiResource('users', UserController::class);
    });

    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::patch('/books/{book}/select', [BookController::class, 'select']);
    Route::patch('/books/{book}/unselect', [BookController::class, 'unselect']);
    Route::patch('/books/{book}/rate', [BookController::class, 'rate']);
});
