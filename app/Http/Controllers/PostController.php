<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Hiển thị trang quản lý bài viết
    public function index(Request $request)
    {
        $query = Post::with(['images', 'category'])->latest();

        // Tìm kiếm theo tiêu đề hoặc mô tả ngắn
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục cha và danh mục con nếu có
        $parentCategoryId = $request->input('category');
        $childCategoryId = $request->input('child_category');

        if ($childCategoryId) {
            // Lọc theo danh mục con cụ thể
            $query->where('category_id', $childCategoryId);
        } elseif ($parentCategoryId) {
            // Lấy tất cả ID danh mục con và danh mục cha được chọn
            $categoryIds = Category::where('id', $parentCategoryId)
                ->orWhere('parent_id', $parentCategoryId)
                ->pluck('id')
                ->toArray();

            $query->whereIn('category_id', $categoryIds);
        }

        $posts = $query->get();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $response = response()->view('posts.index', compact('posts', 'categories'));
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        return $response;
    }

    // Hiển thị trang chi tiết bài viết dành cho user
    public function showForUser($id)
    {
        $post = Post::with(['category', 'images'])->findOrFail($id);

        $suggestedPosts = collect();

        if ($post->category_id) {
            // Lấy 2 bài viết gợi ý cùng danh mục, không lấy bài hiện tại
            $suggestedPosts = Post::with('category')
                ->where('category_id', $post->category_id)
                ->where('id', '!=', $post->id)
                ->limit(2)
                ->get();
        }

        // Nếu không có bài viết gợi ý cùng danh mục hoặc không có category, lấy 2 bài viết mới nhất không phải bài hiện tại
        if ($suggestedPosts->isEmpty()) {
            $suggestedPosts = Post::with('category')
                ->where('id', '!=', $post->id)
                ->latest()
                ->limit(2)
                ->get();
        }

        return view('posts.user_show', compact('post', 'suggestedPosts'));
    }

    // Hiển thị danh mục bài viết
    public function danhmuc(Request $request)
    {
        $query = Post::with(['images', 'category'])->latest();

        // Filter by category if provided
        if ($request->filled('category')) {
            $categoryId = $request->input('category');

            // Get all descendant category IDs including the selected category
            $categoryIds = Category::where('id', $categoryId)
                ->orWhere('parent_id', $categoryId)
                ->pluck('id')
                ->toArray();

            $query->whereIn('category_id', $categoryIds);
        }

        $posts = $query->get();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $response = response()->view('danhmuc', compact('posts', 'categories'));
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        return $response;
    }

    // Server-side processing for danhmuc datatable
    public function danhmucData(Request $request)
    {
        $columns = [
            0 => 'category_id',
            1 => 'title',
            2 => 'short_description',
            3 => 'author_name',
            4 => 'created_at',
        ];

        $totalData = Post::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 4);
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDir = $request->input('order.0.dir', 'desc');
        $searchValue = $request->input('search.value');

        $query = Post::with('category');

        // Filter by parent category
        $parentCategoryId = $request->input('parent_category_id');
        if ($parentCategoryId) {
            $categoryIds = Category::where('id', $parentCategoryId)
                ->orWhere('parent_id', $parentCategoryId)
                ->pluck('id')
                ->toArray();
            $query->whereIn('category_id', $categoryIds);
        }

        // Filter by child category
        $childCategoryId = $request->input('child_category_id');
        if ($childCategoryId) {
            $query->where('category_id', $childCategoryId);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                  ->orWhere('short_description', 'like', "%{$searchValue}%");
            });
        }

        $totalFiltered = $query->count();

        $posts = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        foreach ($posts as $post) {
            $parentName = $post->category && $post->category->parent ? $post->category->parent->name : ($post->category ? $post->category->name : '');
            $childName = $post->category && $post->category->parent ? $post->category->name : '';

            $data[] = [
                $parentName,
                $childName,
                Str::limit($post->title, 50),
                Str::limit($post->short_description, 50),
                $post->author_name ?? 'Không xác định',
                $post->created_at->format('d/m/Y'),
                view('posts.partials.actions', compact('post'))->render(),
            ];
        }

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    // Hiển thị form tạo bài viết mới
    public function create()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('posts.create', compact('categories'));
    }

    // Lưu bài viết mới
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'author_name' => 'nullable|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'required|array|min:2',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = new Post();
        $post->title = $validatedData['title'];
        $post->short_description = $validatedData['short_description'] ?? null;
        $post->content = $validatedData['content'];
        $post->category_id = $validatedData['category_id'] ?? null;
        $post->author_name = $validatedData['author_name'] ?? null;

        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('post_banners', 'public');
            $post->banner = $bannerPath;
        }

        $post->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('post_images', 'public');
                $post->images()->create(['image' => $path]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được tạo thành công.');
    }

    // Hiển thị chi tiết bài viết
    public function show($id)
    {
        $post = Post::with(['category', 'images'])->findOrFail($id);
        return view('posts.show', compact('post'));
    }

    // Hiển thị form chỉnh sửa bài viết
    public function edit($id)
    {
        $post = Post::with('category')->findOrFail($id);
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('posts.edit', compact('post', 'categories'));
    }

    // Cập nhật bài viết
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'author_name' => 'nullable|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = Post::findOrFail($id);
        $post->title = $validatedData['title'];
        $post->short_description = $validatedData['short_description'] ?? null;
        $post->content = $validatedData['content'];
        $post->category_id = $validatedData['category_id'] ?? null;
        $post->author_name = $validatedData['author_name'] ?? null;

        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('post_banners', 'public');
            $post->banner = $bannerPath;
        }

        $post->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('post_images', 'public');
                $post->images()->create(['image' => $path]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được cập nhật thành công.');
    }

    // Xoá bài viết
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được xoá thành công.');
    }
}
