<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
        <h1 class="text-3xl font-semibold mb-6 whitespace-normal">Chỉnh sửa bài viết</h1>

        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block font-medium text-gray-700">Tiêu đề<span class="text-red-600"> *(Bắt buộc)</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="short_description" class="block font-medium text-gray-700">Mô tả ngắn<span class="text-red-600"> *(Bắt buộc)</span></label>
                <textarea name="short_description" id="short_description" rows="3" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('short_description', $post->short_description) }}</textarea>
                @error('short_description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="content" class="block font-medium text-gray-700">Nội dung bài viết<span class="text-red-600"> *(Bắt buộc)</span></label>
                <textarea name="content" id="content" rows="10" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="author_name" class="block font-medium text-gray-700">Tên tác giả<span class="text-red-600"> *(Bắt buộc)</span></label>
                <input type="text" name="author_name" id="author_name" value="{{ old('author_name', $post->author_name) }}" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
                @error('author_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="banner" class="block font-medium text-gray-700">Banner bài viết<span class="text-red-600"> *(Bắt buộc)</span></label>
                @if($post->banner)
                    <img src="{{ asset('storage/' . $post->banner) }}" alt="Banner" class="rounded mb-2" style="width: 500px; height: 300px; object-fit: cover;">
                    <label class="inline-flex items-center space-x-2">
                        <input type="checkbox" name="delete_banner" value="1" class="form-checkbox text-red-600">
                        <span class="text-red-600 text-sm cursor-pointer"> Xóa banner</span>
                    </label>
                @endif
                <input type="file" name="banner" id="banner" accept="image/*"
                    class="mt-1 block w-full" />
                @error('banner')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="gallery" class="block font-medium text-gray-700">Ảnh hiển thị bài viết (Gallery<span class="text-red-600"> *(Bắt buộc)</span></label>
                @if($post->images->count())
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                        @foreach($post->images as $image)
                            <div class="relative group border rounded p-1">
                                <img src="{{ asset('storage/' . $image->image) }}" alt="Gallery Image" class="rounded mb-1" style="width: 500px; height: 300px; object-fit: cover;">
                                <label class="inline-flex items-center space-x-2">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="form-checkbox text-red-600">
                                    <span class="text-red-600 text-sm cursor-pointer">Xóa ảnh</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple
                    class="mt-1 block w-full" />
                @error('gallery.*')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="parentCategoryFilter" class="block font-medium text-gray-700">Danh mục cha<span class="text-red-600"> *(Bắt buộc)</span></label>
                <select id="parentCategoryFilter" class="border border-gray-300 rounded px-3 py-2" required>
                    <option value="">-- Chọn danh mục cha --</option>
                    @foreach($categories as $parentCategory)
                        <option value="{{ $parentCategory->id }}" {{ $post->category && $post->category->parent && $post->category->parent->id == $parentCategory->id ? 'selected' : '' }}>
                            {{ $parentCategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="childCategoryFilter" class="block font-medium text-gray-700">Danh mục con<span class="text-red-600"> *(Bắt buộc)</span></label>
                <select name="category_id" id="childCategoryFilter" class="border border-gray-300 rounded px-3 py-2" required>
                    <option value="">-- Chọn danh mục con --</option>
                    @foreach($categories as $parentCategory)
                        @if($parentCategory->children)
                            @foreach($parentCategory->children as $childCategory)
                                <option value="{{ $childCategory->id }}" {{ old('category_id', $post->category_id) == $childCategory->id ? 'selected' : '' }}>
                                    {{ $childCategory->name }}
                                </option>
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="text-center">
                <button type="submit"
                    class="px-6 py-2 bg-white text-black border border-gray-400 rounded hover:bg-gray-100">Cập nhật bài viết</button>
            </div>
        </form>
    </div>

    <!-- Include CKEditor from CDN for WYSIWYG editor -->
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if ($errors->has('gallery'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Yêu cầu nhập tối thiểu 2 ảnh Gallery',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if ($errors->has('banner'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Không được để trống banner vui lòng chọn ảnh thay thế',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

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

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            var categories = @json($categories);

            function updateChildCategories(parentId) {
                var childSelect = $('#childCategoryFilter');
                childSelect.empty();
                childSelect.append('<option value="">-- Chọn danh mục con --</option>');
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
                // Reset child filter selection when parent changes
                $('#childCategoryFilter').val('');
            });

            // Initialize child categories on page load
            updateChildCategories($('#parentCategoryFilter').val());

            // Ensure child category selection is valid on page load
            var selectedParentId = $('#parentCategoryFilter').val();
            var selectedChildId = $('#childCategoryFilter').val();
            var validChildIds = [];

            if (selectedParentId) {
                var parent = categories.find(function(cat) {
                    return cat.id == selectedParentId;
                });
                if (parent && parent.children) {
                    validChildIds = parent.children.map(function(child) {
                        return child.id.toString();
                    });
                }
            }

            if (selectedChildId && !validChildIds.includes(selectedChildId)) {
                $('#childCategoryFilter').val('');
            }
        });
    </script>
</x-app-layout>
