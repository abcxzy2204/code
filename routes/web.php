<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::delete('/posts/{post}/images/{image}', [PostController::class, 'destroyImage'])->name('posts.images.destroy');

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Models\Post;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    // Chuẩn bị dữ liệu cho biểu đồ
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/posts/export', [PostController::class, 'export'])->name('posts.export');

require __DIR__.'/auth.php';



// Đã import PostController ở đầu file, không cần import lại ở đây
// Route::resource('posts', PostController::class);

// Thêm resource route cho posts trong group middleware auth
Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class);

    

});



