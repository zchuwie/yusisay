<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ReportController as AdminReportController; // Aliased Admin Report Controller
use App\Http\Controllers\Admin\CensoredWordController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/admin', function () {
    return view('admin');
})->name('admin');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/search-users', [ChatController::class, 'search'])->name('users.search');


    Route::get('/chat/{conversation}', [ChatController::class, 'show']);
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
    Route::post('/chat/start', [ChatController::class, 'startConversation']);

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/history', [PostController::class, 'history'])->name('posts.history');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

});


Route::prefix('admin')->name('admin.')->group(function () {
    // Admin View Routes (admin/dashboard, admin/users, admin/reports)
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [AdminController::class, 'user'])->name('user');
    Route::get('reports', [AdminController::class, 'report'])->name('report');
    Route::get('/api/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::put('/api/reports/{reportId}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    
    // Censored Words API
    Route::get('/api/censored-words', [CensoredWordController::class, 'index'])->name('censored_words.index');
    Route::post('/api/censored-words', [CensoredWordController::class, 'store'])->name('censored_words.store');
    Route::delete('/api/censored-words/{censored_word}', [CensoredWordController::class, 'destroy'])->name('censored_words.destroy');
    
    // Users API
    Route::get('/api/users', [UserController::class, 'index'])->name('users.index');
});-

require __DIR__ . '/auth.php';