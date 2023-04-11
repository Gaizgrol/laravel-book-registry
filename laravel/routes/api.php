<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Middleware\AuthMiddleware;
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

Route::post('v1/auth/token', [AuthController::class, 'auth']);
Route::get('v1/livros', [BookController::class, 'index'])->middleware(AuthMiddleware::class);
Route::post('v1/livros', [BookController::class, 'create'])->middleware(AuthMiddleware::class);
Route::post('v1/livros/{id}/importar-indices-xml', [BookController::class, 'queueJob'])->where('id', '[0-9]+')->middleware(AuthMiddleware::class);
