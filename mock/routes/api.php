<?php

use App\Http\Controllers\MockController;
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

Route::middleware('basicauth')->post('/subscribe/{os}', [MockController::class, 'mock']);
Route::post('/hook/{applicationId}', [MockController::class, 'hook']);
