<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route; 

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/admin', function () {
    return view('admin');
})->name('admin');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/search', [ChatController::class, 'search']);
    Route::get('/chat/{conversation}', [ChatController::class, 'show']);
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
    Route::post('/chat/start', [ChatController::class, 'startConversation']);

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/history', [PostController::class, 'history'])->name('posts.history');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');  
});

require __DIR__ . '/auth.php';
