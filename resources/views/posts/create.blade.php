<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow" style="background-color: #2451a3;">
        <h1 class="text-4xl font-semibold mb-6 text-center" style="color: white;">Thêm mới bài viết</h1>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block font-medium text-white">Tiêu đề<span class="text-red-600"> *(Bắt buộc)</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="short_description" class="block font-medium text-white">Mô tả ngắn<span class="text-red-600"> *(Bắt buộc)</span></label>
                <textarea name="short_description" id="short_description" rows="3" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('short_description') }}</textarea>
                @error('short_description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="content" class="block font-medium text-white">Nội dung bài viết<span class="text-red-600"> *(Bắt buộc)</span></label>
                <textarea name="content" id="content" rows="10" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="author_name" class="block font-medium text-white">Tên tác giả<span class="text-red-600"> *(Bắt buộc)</span></label>
                <input type="text" name="author_name" id="author_name" value="{{ old('author_name') }}" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
                @error('author_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="parent_category_id" class="block font-medium text-white">Danh mục cha<span class="text-red-600"> *(Bắt buộc)</span></label>
                <select name="parent_category_id" id="parent_category_id" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
                    <option value="">-- Chọn danh mục cha --</option>
                    @foreach($categories as $category)
                        @if(!$category->parent_id)
                            <option value="{{ $category->id }}" {{ old('parent_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('parent_category_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="child_category_id" class="block font-medium text-white">Danh mục con<span class="text-red-600"> *(Bắt buộc)</span></label>
                <select name="category_id" id="child_category_id" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" required>
                    <option value="">-- Chọn danh mục con --</option>
                </select>
                @error('category_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="banner" class="block font-medium text-white">Banner bài viết<span class="text-red-600"> *(Bắt buộc)</span></label>
                <input type="file" name="banner" id="banner" accept="image/*" required
                    class="mt-1 block w-full" />
                @error('banner')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="images" class="block font-medium text-white">Ảnh hiển thị bài viết (Gallery)<span class="text-red-600"> *(Bắt buộc)</span></label>
                <input type="file" name="images[]" id="images" accept="image/*" multiple required
                    class="mt-1 block w-full" />
                @error('images.*')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="text-center">
                <button type="submit"
                    class="px-6 py-2 bg-white text-black rounded hover:bg-gray-100">Lưu bài viết</button>
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

    <script>
        var categories = @json($categories);

        function updateChildCategories(parentId) {
            var childSelect = document.getElementById('child_category_id');
            childSelect.innerHTML = '<option value="">-- Chọn danh mục con --</option>';
            if (!parentId) {
                childSelect.disabled = true;
                return;
            }
            var parent = categories.find(function(cat) {
                return cat.id == parentId;
            });
            if (parent && parent.children) {
                parent.children.forEach(function(child) {
                    var option = document.createElement('option');
                    option.value = child.id;
                    option.text = child.name;
                    childSelect.appendChild(option);
                });
                childSelect.disabled = false;
            } else {
                childSelect.disabled = true;
            }
        }

        document.getElementById('parent_category_id').addEventListener('change', function() {
            updateChildCategories(this.value);
        });

        // Initialize child categories on page load if parent selected
        updateChildCategories(document.getElementById('parent_category_id').value);
    </script>

    @if ($errors->has('images'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Yêu cầu nhập tối thiểu 2 ảnh Gallery',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

</x-app-layout>
