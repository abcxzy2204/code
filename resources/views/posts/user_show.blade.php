<x-app-layout>
    <div class="min-h-screen bg-gray-200 p-6">
        <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
            <!-- Tiêu đề bài viết và tag danh mục -->
            <div class="mb-4 text-center">
                <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>
                @if($post->category)
                    <span class="inline-block bg-blue-500 text-white px-3 py-1 rounded">{{ $post->category->name }}</span>
                @endif
            </div>

            <!-- Nội dung bài viết -->
            <div class="prose max-w-full mb-6">
                {!! $post->content !!}
            </div>

            <!-- Ảnh Gallery -->
            @if($post->images->count())
                <div class="mb-6 space-y-4 flex flex-col items-center">
                    @foreach($post->images as $image)
                        <img src="{{ asset('storage/' . $image->image) }}" alt="Ảnh bài viết" class="rounded shadow cursor-pointer" style="width: 600px; height: 400px; object-fit: cover;" onclick="openModal(this.src)" />
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Gợi ý bài viết tương tự -->
        <div class="max-w-4xl mx-auto mt-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-700">Gợi ý bài viết tương tự</h2>
            <div class="flex justify-center" style="gap: 100px;">
                @foreach($suggestedPosts as $suggested)
                    <a href="{{ route('posts.user_show', $suggested->id) }}" class="flex bg-white rounded shadow overflow-hidden hover:shadow-lg transition-shadow p-2" style="width: 300px border: 1px solid; display: inline-block;">
                        @if($suggested->banner)
                            <img src="{{ asset('storage/' . $suggested->banner) }}" alt="Banner" class="object-cover" style="width: 150px; height: 150px;" />
                        @else
                            <div class="w-[150px] h-[150px] bg-gray-400 flex items-center justify-center text-white">Banner</div>
                        @endif
                        <div class="p-4 w-[calc(100%-150px)]">
                            <h3 class="font-semibold text-gray-800">{{ $suggested->title }}</h3>
                            <p class="text-gray-600 text-sm">{{ $suggested->short_description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal for image zoom -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50" onclick="closeModal()">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-4xl font-bold z-50 leading-none">&times;</button>
        <img id="modalImage" src="" alt="Zoomed Image" class="rounded shadow-lg" style="max-height: 70vh; max-width: 70vw;" onclick="event.stopPropagation()">
    </div>

    <script>
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modalImg.src = src;
            modal.classList.remove('hidden');
        }
        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            const modalImg = document.getElementById('modalImage');
            modalImg.src = '';
        }
    </script>
</x-app-layout>
