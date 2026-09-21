<?php
// routes/api.php — consumed by the Vedha Flutter app

use App\Http\Controllers\Api\AppApiController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [CustomerAuthController::class, 'register']);
Route::post('/login', [CustomerAuthController::class, 'login']);
Route::post('/social-login', [CustomerAuthController::class, 'socialLogin']);
Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword']);
Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword']);

Route::get('/app-info', [AppApiController::class, 'appInfo']);
Route::get('/app-version', [AppApiController::class, 'appVersion']);
Route::get('/onboarding-screens', [AppApiController::class, 'onboarding']);
Route::get('/categories', [AppApiController::class, 'categories']);
Route::get('/authors', [AppApiController::class, 'authors']);
Route::get('/authors/{author}', [AppApiController::class, 'authorDetail']);
Route::get('/books', [AppApiController::class, 'books']);
Route::get('/books/{book}', [AppApiController::class, 'bookDetail']);
Route::get('/trending-books', [AppApiController::class, 'trending']);
Route::post('/book-suggestions', [AppApiController::class, 'storeSuggestion']);
Route::get('/subscription-plans', [SubscriptionController::class, 'plans']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/me', [CustomerAuthController::class, 'me']);
    Route::put('/me', [CustomerAuthController::class, 'updateMe']);
    Route::post('/books/{book}/reviews', [AppApiController::class, 'storeReview']);
    Route::post('/books/{book}/like', [AppApiController::class, 'toggleLike']);
    Route::get('/liked-books', [AppApiController::class, 'likedBooks']);
    Route::get('/playlists', [PlaylistController::class, 'index']);
    Route::post('/playlists', [PlaylistController::class, 'store']);
    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show']);
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy']);
    Route::post('/playlists/{playlist}/toggle-book', [PlaylistController::class, 'toggleBook']);
    Route::get('/subscriptions/status', [SubscriptionController::class, 'status']);
    Route::post('/subscriptions/create-order', [SubscriptionController::class, 'createOrder']);
    Route::post('/subscriptions/verify', [SubscriptionController::class, 'verify']);
});

