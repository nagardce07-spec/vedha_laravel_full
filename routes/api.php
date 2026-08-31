<?php
// routes/api.php — consumed by the Vedha Flutter app

use App\Http\Controllers\Api\AppApiController;
use Illuminate\Support\Facades\Route;

Route::get('/app-info', [AppApiController::class, 'appInfo']);
Route::get('/onboarding-screens', [AppApiController::class, 'onboarding']);
Route::get('/categories', [AppApiController::class, 'categories']);
Route::get('/authors', [AppApiController::class, 'authors']);
Route::get('/authors/{author}', [AppApiController::class, 'authorDetail']);
Route::get('/books', [AppApiController::class, 'books']);
Route::get('/books/{book}', [AppApiController::class, 'bookDetail']);
Route::get('/trending-books', [AppApiController::class, 'trending']);
Route::post('/book-suggestions', [AppApiController::class, 'storeSuggestion']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books/{book}/reviews', [AppApiController::class, 'storeReview']);
    Route::post('/books/{book}/like', [AppApiController::class, 'toggleLike']);
    Route::get('/liked-books', [AppApiController::class, 'likedBooks']);
});
