<?php

use App\Http\Controllers\Admin\{
    AdmobSettingController, AppSettingController, AuthorController, BookController,
    BookLikeController, BookReviewController, BookSuggestionController, CategoryController,
    CustomerController, DashboardController, GeneralSettingController, NotificationController,
    OnboardingScreenController, PageController, QuickShareSettingController, TrendingBookController
};
use App\Http\Controllers\Auth\AdminLoginController;
use Illuminate\Support\Facades\Route;

// ---- Auth (Welcome Back / Login to your Dashboard) ----
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminLoginController::class, 'login']);
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

// ---- Everything below requires an authenticated admin ----
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Authors
    Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
    Route::post('/authors', [AuthorController::class, 'store'])->name('authors.store');
    Route::put('/authors/{author}', [AuthorController::class, 'update'])->name('authors.update');
    Route::delete('/authors/{author}', [AuthorController::class, 'destroy'])->name('authors.destroy');

    // Customers (read-only)
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Books
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::patch('/books/{book}/toggle/{field}', [BookController::class, 'toggle'])->name('books.toggle');

    // Chapters (nested under a book)
    Route::get('/books/{book}/chapters', [BookController::class, 'chapters'])->name('books.chapters');
    Route::post('/books/{book}/chapters', [BookController::class, 'storeChapter'])->name('books.chapters.store');
    Route::put('/books/{book}/chapters/{chapter}', [BookController::class, 'updateChapter'])->name('books.chapters.update');
    Route::delete('/books/{book}/chapters/{chapter}', [BookController::class, 'destroyChapter'])->name('books.chapters.destroy');

    // Book Reviews
    Route::get('/reviews', [BookReviewController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{review}', [BookReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [BookReviewController::class, 'destroy'])->name('reviews.destroy');

    // Book Likes (read-only)
    Route::get('/likes', [BookLikeController::class, 'index'])->name('likes.index');

    // Trending Books
    Route::get('/trending-books', [TrendingBookController::class, 'index'])->name('trending.index');
    Route::get('/trending-books/search', [TrendingBookController::class, 'search'])->name('trending.search');
    Route::post('/trending-books', [TrendingBookController::class, 'store'])->name('trending.store');
    Route::patch('/trending-books/reorder', [TrendingBookController::class, 'reorder'])->name('trending.reorder');
    Route::delete('/trending-books/{trendingBook}', [TrendingBookController::class, 'destroy'])->name('trending.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    Route::put('/notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
    Route::post('/notifications/{notification}/send', [NotificationController::class, 'send'])->name('notifications.send');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Onboarding Screens
    Route::get('/onboarding-screens', [OnboardingScreenController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding-screens', [OnboardingScreenController::class, 'store'])->name('onboarding.store');
    Route::put('/onboarding-screens/{screen}', [OnboardingScreenController::class, 'update'])->name('onboarding.update');
    Route::patch('/onboarding-screens/reorder', [OnboardingScreenController::class, 'reorder'])->name('onboarding.reorder');
    Route::delete('/onboarding-screens/{screen}', [OnboardingScreenController::class, 'destroy'])->name('onboarding.destroy');

    // Book Suggestions
    Route::get('/book-suggestions', [BookSuggestionController::class, 'index'])->name('suggestions.index');
    Route::post('/book-suggestions/{suggestion}/approve', [BookSuggestionController::class, 'approve'])->name('suggestions.approve');
    Route::delete('/book-suggestions/{suggestion}', [BookSuggestionController::class, 'destroy'])->name('suggestions.destroy');

    // Admob
    Route::get('/admob', [AdmobSettingController::class, 'edit'])->name('admob.edit');
    Route::put('/admob', [AdmobSettingController::class, 'update'])->name('admob.update');

    // App Settings (theme colors)
    Route::get('/app-settings', [AppSettingController::class, 'edit'])->name('appsettings.edit');
    Route::put('/app-settings', [AppSettingController::class, 'update'])->name('appsettings.update');

    // Quick Share
    Route::get('/quick-share', [QuickShareSettingController::class, 'edit'])->name('quickshare.edit');
    Route::put('/quick-share/scheme', [QuickShareSettingController::class, 'updateScheme'])->name('quickshare.scheme');
    Route::put('/quick-share/android', [QuickShareSettingController::class, 'updateAndroid'])->name('quickshare.android');
    Route::post('/quick-share/android/sha-keys', [QuickShareSettingController::class, 'addShaKey'])->name('quickshare.sha.add');
    Route::delete('/quick-share/android/sha-keys/{shaKey}', [QuickShareSettingController::class, 'deleteShaKey'])->name('quickshare.sha.delete');
    Route::get('/quick-share/android/validate', [QuickShareSettingController::class, 'validateAndroid'])->name('quickshare.android.validate');
    Route::put('/quick-share/ios', [QuickShareSettingController::class, 'updateIos'])->name('quickshare.ios');
    Route::get('/quick-share/ios/validate', [QuickShareSettingController::class, 'validateIos'])->name('quickshare.ios.validate');

    // General Settings
    Route::get('/settings', [GeneralSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/admin', [GeneralSettingController::class, 'updateAdmin'])->name('settings.admin');
    Route::put('/settings/password', [GeneralSettingController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/storage', [GeneralSettingController::class, 'updateStorage'])->name('settings.storage');
    Route::put('/settings/email', [GeneralSettingController::class, 'updateEmail'])->name('settings.email');

    // Privacy Policy / Terms of Uses
    Route::get('/privacy-policy', [PageController::class, 'editPrivacy'])->name('privacy.edit');
    Route::put('/privacy-policy', [PageController::class, 'updatePrivacy'])->name('privacy.update');
    Route::get('/terms-of-uses', [PageController::class, 'editTerms'])->name('terms.edit');
    Route::put('/terms-of-uses', [PageController::class, 'updateTerms'])->name('terms.update');
});

Route::get('/', fn () => redirect()->route('admin.dashboard'));
