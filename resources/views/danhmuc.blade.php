<x-app-layout>
<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow" style="margin-top: 30px; background-color: #2451a3;">
<h1 class="text-4xl font-semibold mb-6 text-center" style="color: white;">Danh sách bài viết</h1>
    <div class="flex justify-end space-x-8 mb-4">
         <a href="{{ route('categories.create') }}" 
         style="border: 1px solid #16a34a; border-radius: 0.375rem; background-color: #16a34a; color: white; padding: 0.5rem 1rem;">
         Tạo danh mục cha
         </a>
         <a href="{{ route('categories.create', ['parent_id' => $categories->first()->id ?? '']) }}" 
         style="border: 1px solid #1e40af; border-radius: 0.375rem; background-color: #16a34a; color: white; padding: 0.5rem 1rem;">
         Tạo danh mục con
         </a>
     </div>
       <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            @if ($posts->isEmpty())
                <p class="text-gray-600 text-center text-lg">📭 Không có bài viết nào.</p>
            @else
                <div class="mb-4 flex items-center space-x-4">
                    <div>
                        <label for="parentCategoryFilter" class="font-medium text-gray-700">Lọc theo danh mục cha:</label>
                        <select id="parentCategoryFilter" class="border border-gray-300 rounded px-3 py-2">
                            <option value="">Tất cả</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="childCategoryFilter" class="font-medium text-gray-700">Lọc theo danh mục con:</label>
                        <select id="childCategoryFilter" class="border border-gray-300 rounded px-3 py-2" disabled>
                            <option value="">Tất cả</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto" style="max-width: 100vw;">
                    <style>
                        /* Cho chữ trong các ô của bảng được xuống dòng và không tràn */
                        #postsTable td,
                        #postsTable th {
                            white-space: normal !important;
                            word-wrap: break-word;
                            word-break: break-word;
                            overflow-wrap: break-word;
                            max-width: 200px;
                        }
                        /* Đảm bảo bảng không bị tràn ra ngoài */
                        #postsTable {
                            table-layout: fixed !important;
                            width: 100% !important;
                        }
                    </style>
                    <table id="postsTable" class="table-auto w-full border-collapse border border-gray-200" style="width: 100% !important;">
                    <thead>
                        <tr style="background-color: #2451a3; color: white;">
                            <th class="p-4 border">Danh mục cha</th>
                            <th class="p-4 border">Danh mục con</th>
                            <th class="p-4 border">Tiêu đề</th>
                            <th class="p-4 border">Mô tả ngắn</th>
                            <th class="p-4 border">Tên tác giả</th>
                            <th class="p-4 border">Ảnh banner</th>
                            <th class="p-4 border">Ngày tạo</th>
                            <th class="p-4 border">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
@foreach ($posts as $post)
    <tr class="border border-gray-200 text-gray-700">
        <td class="p-4">{{ $post->category->parent ? $post->category->parent->name : $post->category->name }}</td>
        <td class="p-4">{{ $post->category->parent ? $post->category->name : '' }}</td>
        <td class="p-4">{{ Str::limit($post->title, 15) }}</td>
        <td class="p-4">{{ Str::limit($post->short_description, 15) }}</td>
        <td class="p-4">{{ $post->author_name ?? 'Không xác định' }}</td>
        <td class="p-4">
            @if($post->banner)
                <img src="{{ asset('storage/' . $post->banner) }}" alt="Banner" class="h-12 w-12 rounded" style="width: 50px; height: 50px;">
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
        @endif
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

        // Category filters
        var categories = @json($categories);

        function updateChildCategories(parentId) {
            var childSelect = $('#childCategoryFilter');
            childSelect.empty();
            childSelect.append('<option value="">Tất cả</option>');
            if (!parentId) {
                childSelect.prop('disabled', true);
                return;
            }
            var parent = categories.find(function(cat) {
                return cat.id == parentId;
            });
            if (parent && parent.children) {
                parent.children.forEach(function(child) {
                    childSelect.append('<option value="' + child.id + '">' + child.name + '</option>');
                });
                childSelect.prop('disabled', false);
            } else {
                childSelect.prop('disabled', true);
            }
        }

        $('#parentCategoryFilter').on('change', function () {
            updateChildCategories($(this).val());
            // Reset child filter
            $('#childCategoryFilter').val('');
            table.rows().every(function () {
                var data = this.data();
                var parentCategoryId = $('#parentCategoryFilter').val();
                var childCategoryId = $('#childCategoryFilter').val();

                var parentName = data[0];
                var childName = data[1];

                var parentMatch = !parentCategoryId || parentName == categories.find(cat => cat.id == parentCategoryId).name;
                var childMatch = !childCategoryId || childName == categories.find(cat => cat.id == childCategoryId)?.name;

                if (parentMatch && childMatch) {
                    $(this.node()).show();
                } else {
                    $(this.node()).hide();
                }
            });
        });

        $('#childCategoryFilter').on('change', function () {
            table.rows().every(function () {
                var data = this.data();
                var parentCategoryId = $('#parentCategoryFilter').val();
                var childCategoryId = $('#childCategoryFilter').val();

                var parentName = data[0];
                var childName = data[1];

                var parentMatch = !parentCategoryId || parentName == categories.find(cat => cat.id == parentCategoryId).name;
                var childMatch = !childCategoryId || childName == categories.find(cat => cat.id == childCategoryId)?.name;

                if (parentMatch && childMatch) {
                    $(this.node()).show();
                } else {
                    $(this.node()).hide();
                }
            });
        });

        // Initialize child categories if parent selected
        updateChildCategories($('#parentCategoryFilter').val());
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
