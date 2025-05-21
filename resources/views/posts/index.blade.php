<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 bg-white rounded shadow" style="margin-top: 30px;">
        <h1 class="text-3xl font-semibold mb-6">Danh sách bài viết</h1>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

       <div class="flex justify-end space-x-8 mb-4">
           
            <a href="{{ route('posts.export') }}" 
               style="border: 1px solid gray; border-radius: 0.375rem; background-color: green; color: white; padding: 0.5rem 1rem;">
               Xuất file Excel
            </a>
        </div>
       <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            @if ($posts->isEmpty())
                <p class="text-gray-600 text-center text-lg">📭 Không có bài viết nào.</p>
            @else
                <div class="overflow-x-auto">
                    <table id="postsTable" class="table-auto w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-green-600 text-black">
                    <th class="p-4 border">Tiêu đề</th>
                    <th class="p-4 border">Mô tả ngắn</th>
                    <th class="p-4 border">Tên tác giả</th>
                    <th class="p-4 border">Ngày tạo</th>
                    <th class="p-4 border">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr class="border border-gray-200 text-gray-700">
                                <td class="p-4">{{ $post->title }}</td>
                                <td class="p-4">{{ Str::limit($post->short_description, 100) }}</td>
                                <td class="p-4">{{ $post->user ? $post->user->name : 'Không xác định' }}</td>
                                <td class="p-4">{{ $post->created_at->format('d/m/Y') }}</td>

                                <td class="p-4 flex justify-center space-x-2">
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
            @endif
        </div>

    </div>

    <!-- Thêm CDN jQuery và DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#postsTable').DataTable({
                paging: true,
                
            pageLength: 4,
                searching: true,
                ordering: true,
                responsive: true,
                language: {
                    search: "🔍 Tìm kiếm:",
                    info: "Hiển thị trang _PAGE_ trên _PAGES_",
                    lengthMenu: "Hiển thị _MENU_ bài viết",
                    paginate: {
                        previous: "⬅️",
                        next: "➡️"
                    },
                    emptyTable: "📭 Không có dữ liệu để hiển thị"
                }
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
