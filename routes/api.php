<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminAuthController;

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



Route::post('/login',[AdminAuthController::class,'login']);
Route::post('/register',[AdminAuthController::class,'register']);
Route::get('/posts', [PostController::class, 'index']);
/**
 * Retrieve a specific post by its ID.
 *
 * @param int $id The ID of the post to retrieve.
 * @return \Illuminate\Http\JsonResponse
 */
Route::get('/posts/{id}', [PostController::class, 'show']);

/**
 * Middleware-protected routes for authenticated users.
 *
 * Includes routes for retrieving the authenticated user, logging out, and managing posts (except index and show).
 * Also includes a route for deleting a specific image from a post.
 */
Route::group(['middleware' => ['auth:sanctum']], function () {

/**
 * Log out the authenticated user.
 *
 * @return \Illuminate\Http\Response
 */
Route::post('/logout', [AdminAuthController::class, 'logout']);

/**
 * Resource routes for managing posts, except index and show.
 */
Route::resource('/posts', PostController::class)->except(['index', 'show']);

/**
 * Delete a specific image from a post.
 *
 * @param int $postId The ID of the post.
 * @param int $imageId The ID of the image to delete.
 * @return \Illuminate\Http\Response
 */
Route::delete('/posts/{postId}/images/{imageId}', [PostController::class, 'deleteImage']);
});

Route::get('/posts/{id}', [PostController::class, 'show']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function () {
        return response()->json("test2");
    });
    Route::post('/logout',[AdminAuthController::class,'logout']);
    Route::resource('/posts', PostController::class)->except(['index','show']);
    Route::delete('/posts/{postId}/images/{imageId}', [PostController::class,'deleteImage']);

});

