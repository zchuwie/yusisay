<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\Admin\ReportController as AdminReportController; // Aliased Admin Report Controller
use App\Http\Controllers\Admin\CensoredWordController; 
use App\Http\Controllers\Admin\UserController; 


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/search', [ChatController::class, 'search']);
    Route::get('/chat/{conversation}', [ChatController::class, 'show']);
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
    Route::post('/chat/start', [ChatController::class, 'startConversation']);

    // Posts & History
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/history', [PostController::class, 'history'])->name('posts.history');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

    // Comments & Reports
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store'); 
});


Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [AdminController::class, 'user'])->name('user');
    Route::get('reports', [AdminController::class, 'report'])->name('report'); 
    Route::get('/api/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::put('/reports/{reportId}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::get('/api/censored-words', [CensoredWordController::class, 'index'])->name('censored_words.index');
    Route::post('/api/censored-words', [CensoredWordController::class, 'store'])->name('censored_words.store');
    Route::delete('/censored-words/{id}', [CensoredWordController::class, 'destroy'])->name('censored_words.destroy');
});


require __DIR__ . '/auth.php';

    Route::get('reports', [AdminController::class, 'report'])->name('report'); 
    Route::get('/api/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::put('/reports/{reportId}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::get('/api/censored-words', [CensoredWordController::class, 'index'])->name('censored_words.index');
    Route::post('/api/censored-words', [CensoredWordController::class, 'store'])->name('censored_words.store');
    Route::delete('/censored-words/{id}', [CensoredWordController::class, 'destroy'])->name('censored_words.destroy');
    Route::get('users', [AdminController::class, 'user'])->name('user'); 
    Route::get('/api/users', [UserController::class, 'index'])->name('users.index'); 

Route::get('/admin/api/users', [App\Http\Controllers\Admin\UserController::class, 'index']);
Route::get('/admin/api/users', [UserController::class, 'index'])->name('admin.api.users');

Route::get('/admin/api/reports', [ReportController::class, 'index'])->name('admin.api.reports');