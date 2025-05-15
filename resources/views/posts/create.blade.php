<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow" style="background: linear-gradient(to right, #a78bfa, #f9a8d4, #fcd34d);">
        <h1 class="text-3xl font-semibold mb-6 text-center" style="width: 131px; line-height: 41px; margin: 0 auto;">Thêm bài viết mới</h1>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block font-medium text-gray-700">Tiêu đề</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="short_description" class="block font-medium text-gray-700">Mô tả ngắn</label>
                <textarea name="short_description" id="short_description" rows="3" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('short_description') }}</textarea>
                @error('short_description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="content" class="block font-medium text-gray-700">Nội dung bài viết</label>
                <textarea name="content" id="content" rows="10" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="banner" class="block font-medium text-gray-700">Banner bài viết</label>
                <input type="file" name="banner" id="banner" accept="image/*"
                    class="mt-1 block w-full" />
                @error('banner')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="gallery" class="block font-medium text-gray-700">Ảnh hiển thị bài viết (Gallery)</label>
                <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple
                    class="mt-1 block w-full" />
                @error('gallery.*')
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
</x-app-layout>
