<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubjectController;

// use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ConferenceController;

use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminAuthController;




Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/register', [AdminAuthController::class, 'register']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/staff', [StaffController::class, 'index']);

///-------------------------Conferences -------------------------------////

Route::get('/conferences',[ConferenceController::class,'index']); // conferences title list
Route::get('/conferences/{id}',[ConferenceController::class,'show']); // conferences detail
///--------------------------------------------------------------------------

/**
 * Retrieve a specific post by its ID.
 *
 * @param int $id The ID of the post to retrieve.
 * @return \Illuminate\Http\JsonResponse
 */
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/subjects', [SubjectController::class, 'index']);

/**
 * Retrieve a specific subject by its ID.
 *
 * @param int $id The ID of the subject to retrieve.
 * @return \Illuminate\Http\JsonResponse
 */
Route::get('/subjects/{id}', [SubjectController::class, 'show']);

/**
 * Retrieve a specific staff by its ID.
 *
 * @param int $id The ID of the staff to retrieve.
 * @return \Illuminate\Http\JsonResponse
 */
Route::get('/staff/{id}', [StaffController::class, 'show']);
/**
 * Middleware-protected routes for authenticated users.
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    /**
     * Log out the authenticated user.
     * @return \Illuminate\Http\Response
     */
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    /**
     * Resource routes for managing posts, except index and show.
     */
    Route::group(['middleware' => ['xss']], function () {
        Route::resource('/posts', PostController::class)->except(['index', 'show']);
        Route::resource('/subjects', SubjectController::class)->except(['index', 'show']);
        Route::resource('/teachers', TeacherController::class)->except(['index', 'show']);
        Route::resource('/conferences', ConferenceController::class)->except(['index', 'show']);
    });
    /**
     * Delete a specific image from a post.
     * @param int $postId The ID of the post.
     * @param int $imageId The ID of the image to delete.
     * @return \Illuminate\Http\Response
     */
});





