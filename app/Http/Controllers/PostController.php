<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PostsExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    // Hiển thị danh sách bài viết  
    public function index(Request $request)
    {
        $query = Post::with('images')->latest();

        // Search by title or short_description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter example: by date or other criteria can be added here

        $posts = $query->paginate(5)->appends($request->all());

        return view('posts.index', compact('posts'));
    }

    // Hiển thị form tạo bài viết mới
    public function create()
    {
        return view('posts.create');
    }

    // Lưu bài viết mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'banner' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
        ]);

        $validated['title'] = strip_tags($validated['title']);
        $validated['short_description'] = strip_tags($validated['short_description']);
        // content có thể chứa html do WYSIWYG

        // $validated['user_id'] = auth()->id();

        // Xử lý upload banner
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $validated['banner'] = $bannerPath;
        }

        $post = Post::create($validated);

        // Xử lý upload gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $imagePath = $image->store('gallery', 'public');
                $post->images()->create(['image' => $imagePath]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Đăng bài thành công!');
    }

    // Hiển thị chi tiết bài viết
    public function show(Post $post)
    {
        $post->load('images');
        return view('posts.show', compact('post'));
    }

    // Hiển thị form chỉnh sửa bài viết
    public function edit(Post $post)
    {
        $post->load('images');
        return view('posts.edit', compact('post'));
    }

    // Cập nhật bài viết
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'banner' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:post_images,id',
            'delete_banner' => 'nullable|boolean',
        ]);

        $validated['title'] = strip_tags($validated['title']);
        $validated['short_description'] = strip_tags($validated['short_description']);
        // content có thể chứa html do WYSIWYG

        // Xử lý xóa ảnh gallery nếu có
        if (!empty($validated['delete_images'])) {
            $imagesToDelete = $post->images()->whereIn('id', $validated['delete_images'])->get();
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            }
        }

        // Xử lý upload banner mới nếu có
        if ($request->hasFile('banner')) {
            // Xóa banner cũ nếu có
            if ($post->banner) {
                Storage::disk('public')->delete($post->banner);
            }
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $validated['banner'] = $bannerPath;
        }

        $post->update($validated);

        // Xử lý upload gallery images mới nếu có
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $imagePath = $image->store('gallery', 'public');
                $post->images()->create(['image' => $imagePath]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    // Xóa bài viết
    public function destroy(Post $post)
    {
        // Xóa banner
        if ($post->banner) {
            Storage::disk('public')->delete($post->banner);
        }
        // Xóa ảnh gallery
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image);
            $image->delete();
        }
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Xóa bài viết thành công!');
    }


public function export()
    {
        // Lấy dữ liệu bài viết
        $posts = Post::select('id', 'title', 'short_description', 'content', 'created_at', 'updated_at')->get();

        // Tạo spreadsheet mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Title');
        $sheet->setCellValue('C1', 'Short Description');
        $sheet->setCellValue('D1', 'Content');
        $sheet->setCellValue('E1', 'Created At');
        $sheet->setCellValue('F1', 'Updated At');

        // Đổ dữ liệu vào từng dòng
        $row = 2;
        foreach ($posts as $post) {
            $sheet->setCellValue('A' . $row, $post->id);
            $sheet->setCellValue('B' . $row, $post->title);
            $sheet->setCellValue('C' . $row, $post->short_description);
            $sheet->setCellValue('D' . $row, $post->content);
            $sheet->setCellValue('E' . $row, $post->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('F' . $row, $post->updated_at->format('Y-m-d H:i:s'));
            $row++;
        }

        // Tạo file excel
        $writer = new Xlsx($spreadsheet);

        // Tạo response stream để tải file
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        // Header để trình duyệt nhận dạng là file excel
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="posts.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

}