<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Post;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Exports\PostsExport;
use Maatwebsite\Excel\Facades\Excel;
// Trang chủ
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', function () {
    $totalPosts = Post::count();
    $latestPostCount = Post::where('created_at', '>=', Carbon::now()->subDay())->count();
    $latestPost = Post::latest()->first();
    $latestPostDate = $latestPost ? $latestPost->created_at->format('d/m/Y') : '-';

    // Lấy số lượng bài viết theo ngày trong 7 ngày gần nhất
    $postsByDate = Post::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
        ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $dates = [];
    $counts = [];
    for ($i = 0; $i < 7; $i++) {
        $date = Carbon::now()->subDays(6 - $i)->format('Y-m-d');
        $dates[] = $date;
        $countForDate = $postsByDate->firstWhere('date', $date);
        $counts[] = $countForDate ? $countForDate->count : 0;
    }

    return view('dashboard', compact('totalPosts', 'latestPostCount', 'latestPostDate', 'dates', 'counts'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Nhóm các route cần đăng nhập
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//xuất file excel
    Route::get('/posts/export', function () {
        return Excel::download(new PostsExport, 'posts.xlsx');
    })->name('posts.export');

    // Xuất file excel
    Route::get('/posts/export', [PostController::class, 'export'])->name('posts.export');  
    // Posts
    Route::resource('posts', PostController::class);

    // Xoá ảnh trong bài viết
    Route::delete('/posts/{post}/images/{image}', [PostController::class, 'destroyImage'])->name('posts.images.destroy');
});


// datatables

Route::get('/posts/partials/index', [DashboardController::class, 'index'])->name('posts.partials.index');
// update
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
//delete post

// Auth routes
require __DIR__ . '/auth.php';
