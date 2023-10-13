<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Chatify\Http\Controllers\MessagesController;
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

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/completeSignIn', [AuthController::class, 'completeSignIn']);
Route::get('/userProfile', [UserController::class, 'userProfile']);
Route::patch('/updateUser', [UserController::class, 'updateUser']);
Route::get('/username', [UserController::class, 'checkUsername']);
Route::get('/users', [UserController::class, 'index']);
// change password
Route::post('/changePassword', [AuthController::class, 'changePassword']);
//upload user photo
Route::post('/uploadUserPhoto', [UserController::class, 'uploadUserPhoto']);
//get user photos
Route::get('/getUserPhotos/{id}', [UserController::class, 'getUserPhotos']);
// get individual user data
Route::get('/getUserData/{id}', [UserController::class, 'getUserData']);

//message routes
Route::post('/token', [UserController::class, 'token']);

//get referer link
Route::get('/referrer', [UserController::class, 'getRefererLink']);

// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'auth'
// ], function ($router) {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/refresh', [AuthController::class, 'refresh']);
//     Route::get('/user-profile', [AuthController::class, 'userProfile']);    
// });
