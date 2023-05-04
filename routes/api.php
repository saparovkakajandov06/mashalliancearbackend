<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ProfileController;
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

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/{id}', [ProfileController::class, 'show']);
    
    Route::post('logout',[AuthController::class, 'logout']);
    Route::get('profile', [ProfileController::class, 'index']);
    Route::get('profile/edit', [ProfileController::class,'edit']);
    Route::patch('profile/update', [ProfileController::class,'update']);
    Route::post('profile/update-avatar', [ProfileController::class,'updateAvatar']);
    
    
    Route::post('chat/send-message', [ChatController::class, 'sendMessage']);
    Route::get('chats', [ChatController::class,'getChats']);
    Route::get('chat/{chat}', [ChatController::class, 'getChat']);
    
    Route::delete('message/delete/{message}', [ChatController::class, 'deleteMessage']);
    Route::delete('chat/delete/{chat}', [ChatController::class, 'deleteChat']);
    
    
});