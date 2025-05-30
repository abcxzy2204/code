<x-app-layout>
<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow" style="margin-top: 30px; background-color: #2451a3;">
<h1 class="text-4xl font-semibold mb-6 text-center" style="color: white;">Danh sách bài viết</h1>
    <div class="flex justify-end space-x-8 mb-4">
         <a href="{{ route('posts.export') }}" 
         style="border: 1px solid #16a34a; border-radius: 0.375rem; background-color: #16a34a; color: white; padding: 0.5rem 1rem;">
         Xuất file Excel
         </a>
     </div>
       <div class="mt-6 bg-white shadow-md rounded-lg p-6">
                <div class="mb-4 flex items-center space-x-2">
                    <label for="categoryFilter" class="font-medium text-gray-700">Lọc theo danh mục:</label>
                    <select id="categoryFilter" class="border border-gray-300 rounded px-3 py-2" name="categoryFilter">
                        <option value="">Tất cả</option>
                        @foreach ($categories as $category)
                            @if(!$category->parent_id)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="overflow-x-auto" style="max-width: 100vw;">
                    <style>
                        /* Prevent overflow and wrap long text in title and short description columns */
#postsTable td:nth-child(1),
#postsTable th:nth-child(1),
#postsTable td:nth-child(2),
#postsTable th:nth-child(2),
#postsTable td:nth-child(3),
#postsTable th:nth-child(3),
#postsTable td:nth-child(4),
#postsTable th:nth-child(4),
#postsTable td:nth-child(5),
#postsTable th:nth-child(5) {
    white-space: normal !important;
    word-wrap: break-word;
    word-break: break-word;
    max-width: 200px;
    overflow-wrap: break-word;
}
/* Force wrapping and width for Danh mục, Tên tác giả, Ảnh banner headers */
#postsTable thead th:nth-child(3),
#postsTable thead th:nth-child(4),
#postsTable thead th:nth-child(5) {
    white-space: normal !important;
    max-width: 150px;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    width: 150px !important;
    height: auto !important;
}
                    </style>
                    <table id="postsTable" class="table-auto w-full border-collapse border border-gray-200" style="width: 100% !important;">
                    <thead>
                        <tr style="background-color: #2451a3; color: white;">
                            <th class="p-4 border">Tiêu đề</th>
                            <th class="p-4 border">Mô tả ngắn</th>
                            <th class="p-4 border">Danh mục</th>
                            <th class="p-4 border">Tên tác giả</th>
                            <th class="p-4 border">Ảnh banner</th>
                            <th class="p-4 border">Ngày tạo</th>
                            <th class="p-4 border">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr class="border border-gray-200 text-gray-700">
                                <td class="p-4">{{ Str::limit($post->title, 15) }}</td>
                                <td class="p-4">{{ Str::limit($post->short_description, 15) }}</td>
                                <td class="p-4">{{ $post->category && $post->category->parent ? $post->category->parent->name : ($post->category ? $post->category->name : '') }}</td>
                                <td class="p-4">{{ $post->author_name ?? 'Không xác định' }}</td>
                                <td class="p-4">
                                    @if($post->images && $post->images->count() > 0)
                                        <img src="{{ asset('storage/' . $post->images->first()->image) }}" alt="Banner" class="h-12 w-12 rounded" style="width: 50px; height: 50px;">
                                    @else
                                        <span>Không có ảnh</span>
                                    @endif
                                </td>
                                <td class="p-4">{{ $post->created_at->format('d/m/Y') }}</td>
                                <td class="p-4 flex items-center justify-center space-x-2">
                                    {{-- Xem --}}
                                    <a href="{{ route('posts.show', $post->id) }}"
                                       class="inline-flex items-center px-2 py-1 text-blue-600 border border-gray-300 hover:bg-blue-100 rounded text-sm">
                                        🔍
                                    </a>
                                    {{-- Sửa --}}
                                    <a href="{{ route('posts.edit', $post->id) }}"
                                       class="inline-flex items-center px-2 py-1 text-yellow-600 border border-gray-300 hover:bg-yellow-100 rounded text-sm">
                                        ✏️ 
                                    </a>
                                    {{-- Xóa --}}
                                    <form action="{{ route('posts.destroy', $post->id) }}"
                                      method="POST"
                                      class="inline delete-post-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-1 text-red-600 border border-gray-300 hover:bg-red-100 rounded text-sm">
                                        🗑️
                                    </button>
                                </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
    
                    </table>
                </div>
        </div>

    </div>

    <!-- Thêm CDN jQuery và DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#postsTable').DataTable({
            paging: true,
            
        pageLength: 4,
            searching: true,
            ordering: true,
            responsive: true,
            columnDefs: [
                { targets: 2, orderable: true }, // Enable ordering on the category column
            ],
            language: {
                search: " Tìm kiếm:",
                info: "Hiển thị trang _PAGE_ trên _PAGES_",
                lengthMenu: "Hiển thị _MENU_ bài viết",
                paginate: {
                    previous: "⬅️",
                    next: "➡️"
                },
                emptyTable: "📭 Không có dữ liệu để hiển thị"
            }
        });

        // Category filter
        $('#categoryFilter').on('change', function () {
            var selected = $(this).val();
            var url = new URL(window.location.href);
            if(selected) {
                url.searchParams.set('category', selected);
            } else {
                url.searchParams.delete('category');
            }
            window.location.href = url.toString();
        });
    });
</script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK'
        });
    </script>
    @endif
<script>
    $(document).ready(function() {
        $(document).on('submit', '.delete-post-form', function(e) {
            e.preventDefault(); // chặn submit form mặc định

            const form = this;

            Swal.fire({
                title: 'Bạn có chắc muốn xóa bài viết này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Có, xóa ngay!',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // nếu xác nhận thì submit form thật
                }
            });
        });
    });
</script>
</x-app-layout>
