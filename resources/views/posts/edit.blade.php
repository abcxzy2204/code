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
                <label for="banner" class="block font-medium text-gray-700">Banner bài viết<span class="text-red-600"> *(Bắt buộc)</span></label>
                @if($post->banner)
                    <img src="{{ asset('storage/' . $post->banner) }}" alt="Banner" class="w-600 h-25 object-cover mb-2 rounded">
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
                                <img src="{{ asset('storage/' . $image->image) }}" alt="Gallery Image" class="w-15 h-30 object-cover rounded mb-1">
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
    
</x-app-layout>
