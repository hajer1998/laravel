<?php

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

/**
 * User API
 */


//login
Route::post('/user/login', [\App\Http\Controllers\UserController::class, 'login']);
//registration
Route::post('/user/register', [\App\Http\Controllers\UserController::class, 'register']);
//get user
Route::get('/user/{id}', [\App\Http\Controllers\UserController::class, 'get']);

/**
 * post API
 */

//get post by id
Route::get('/post/{id}', [\App\Http\Controllers\PostController::class, 'get'])->middleware('auth');
//like post
Route::post('/post/like/{id}', [\App\Http\Controllers\PostController::class, 'like'])->middleware('auth');
//unlike post
Route::post('/post/unlike/{id}', [\App\Http\Controllers\PostController::class, 'unlike'])->middleware('auth');

//delete post
Route::delete('/post/{id}', [\App\Http\Controllers\PostController::class, 'delete'])->middleware('auth');
//create post
Route::post('/post', [\App\Http\Controllers\PostController::class, 'create'])->middleware('auth');
//listing posts
Route::get('/post/', [\App\Http\Controllers\PostController::class, 'listing'])->middleware('auth');
//Edit Post
Route::put('/post/{id}', [\App\Http\Controllers\PostController::class, 'edit'])->middleware('auth');
