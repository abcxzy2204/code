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
        $query = Post::with(['images', 'user'])->latest();

        // Search by title or short_description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter example: by date or other criteria can be added here

        $posts = $query->get();
        $response = response()->view('posts.index', compact('posts'));
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        return $response;
    }

    // DataTables server-side processing endpoint
    public function data(Request $request)
    {
        $columns = [
            0 => 'title',
            1 => 'short_description',
            2 => 'created_at',
        ];

        $totalData = Post::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDir = $request->input('order.0.dir', 'desc');
        $searchValue = $request->input('search.value');

        $query = Post::query();

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                  ->orWhere('short_description', 'like', "%{$searchValue}%");
            });

            $totalFiltered = $query->count();
        }

        $posts = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'title' => $post->title,
                'short_description' => Str::limit($post->short_description, 50),
                'created_at' => $post->created_at->format('d/m/Y'),
                'action' => view('posts.partials.actions', compact('post'))->render(),
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
        return view('posts.create');
    }

    // Lưu bài viết mới
    public function store(Request $request)
    {
        $messages = [
            'gallery.required' => 'Bạn phải thêm ít nhất 2 ảnh cho gallery.',
            'gallery.min' => 'Bạn phải thêm ít nhất 2 ảnh cho gallery.',
            'gallery.array' => 'Gallery phải là một mảng ảnh hợp lệ.',
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'required|array|min:2|max:5',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        $validated['title'] = strip_tags($validated['title']);
        $validated['short_description'] = strip_tags($validated['short_description']);
        // content có thể chứa html do WYSIWYG

        $validated['user_id'] = auth()->id();

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
        return redirect()->route('posts.index')->with('success', 'Tạo bài viết thành công!');
    }

    // Hiển thị chi tiết bài viết
    public function show(Post $post)
    {
        $post->load('images');
        return view('posts.show', compact('post'))  ;
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
        $messages = [
            'gallery.required' => 'Bạn phải thêm ít nhất 2 ảnh cho gallery.',
            'gallery.min' => 'Bạn phải thêm ít nhất 2 ảnh cho gallery.',
            'gallery.array' => 'Gallery phải là một mảng ảnh hợp lệ.',
            'banner.required_if' => 'Khi xóa banner cũ, bạn phải tải lên banner mới.',
        ];

        // Custom validation for gallery count including existing images minus deleted plus new uploads
        $existingImagesCount = $post->images()->count();
        $deleteImagesCount = is_array($request->input('delete_images')) ? count($request->input('delete_images')) : 0;
        $newImagesCount = $request->hasFile('gallery') ? count($request->file('gallery')) : 0;
        $totalImagesCount = $existingImagesCount - $deleteImagesCount + $newImagesCount;

        if ($totalImagesCount < 2) {
            return redirect()->back()
                ->withErrors(['gallery' => 'Bạn phải có ít nhất 2 ảnh trong gallery.'])
                ->withInput();
        }

        // If user wants to delete banner, require new banner upload
        if ($request->input('delete_banner') && !$request->hasFile('banner')) {
            return redirect()->back()
                ->withErrors(['banner' => 'Khi xóa banner cũ, bạn phải tải lên banner mới.'])
                ->withInput();
        }

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:1000',
            'content'           => 'nullable|string',
            'banner'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery'           => 'nullable|array|max:5',
            'gallery.*'         => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images'     => 'nullable|array',
            'delete_images.*'   => 'integer|exists:post_images,id',
            'delete_banner'     => 'nullable|boolean',
        ], $messages);

        $validated['title'] = strip_tags($validated['title']);
        $validated['short_description'] = strip_tags($validated['short_description']);
        // content có thể chứa html do WYSIWYG

        $validated['user_id'] = auth()->id();

        // Xử lý xóa ảnh gallery nếu có
        if (!empty($validated['delete_images'])) {
            $imagesToDelete = $post->images()->whereIn('id', $validated['delete_images'])->get();
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            }
        }

        // Xử lý xóa banner nếu có yêu cầu
        if (!empty($validated['delete_banner']) && $post->banner) {
            Storage::disk('public')->delete($post->banner);
            $post->banner = null;
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
