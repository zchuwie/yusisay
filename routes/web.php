<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\CensoredWordController;
use App\Http\Controllers\Admin\UserController;

// --------------------
// PUBLIC ROUTES
// --------------------
Route::get('/', function () {
    return view('auth.login');
});

// Redirect /admin → /admin/dashboard to avoid route conflict
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

// --------------------
// AUTHENTICATED ROUTES
// --------------------
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/search-users', [ChatController::class, 'search'])->name('users.search');
    Route::get('/chat/{conversation}', [ChatController::class, 'show']);
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
    Route::post('/chat/start', [ChatController::class, 'startConversation']);

    // Posts
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/history', [PostController::class, 'history'])->name('posts.history');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Comments
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Reports
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

// --------------------
// ADMIN ROUTES
// --------------------
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Pages
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [AdminController::class, 'user'])->name('user');
    Route::get('reports', [AdminController::class, 'report'])->name('report');
    Route::get('censored-words', [AdminController::class, 'censoredWords'])->name('censored_words');

    // --- API ROUTES ---

    // Growth Data
    Route::get('api/growth-data', [AdminController::class, 'getGrowthData'])->name('api.growth_data');

    // Reports API
    Route::get('api/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::put('api/reports/{reportId}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::put('api/reports/{postId}/approve', [AdminReportController::class, 'approve'])->name('reports.approve');
    Route::put('api/reports/{postId}/decline', [AdminReportController::class, 'decline'])->name('reports.decline');

    // Censored Words API
    Route::get('api/censored-words', [CensoredWordController::class, 'index'])->name('censored_words.index');
    Route::post('api/censored-words', [CensoredWordController::class, 'store'])->name('censored_words.store');
    Route::delete('api/censored-words/{censored_word}', [CensoredWordController::class, 'destroy'])->name('censored_words.destroy');

    // Users API
    Route::get('api/users', [UserController::class, 'index'])->name('users.index');
    Route::put('api/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('api/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// --------------------
// AUTH ROUTES
// --------------------
require __DIR__ . '/auth.php';
