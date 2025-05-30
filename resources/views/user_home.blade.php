    <x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-4 text-center">Trang chủ người dùng</h1>

        <!-- Search form -->
        <form method="GET" action="{{ route('user.home') }}" class="mb-6 flex justify-center">
            <input
                type="text"
                name="search"
                value="{{ old('search', $search ?? '') }}"
                placeholder="Tìm kiếm bài viết..."
                class="w-full max-w-xs px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1" style="background-color: #4f46e5;">
                Tìm kiếm
            </button>
        </form>

        <!-- Posts list -->
        <div class="space-y-5">
            @forelse ($posts as $post)
                <article class="bg-white shadow rounded-lg overflow-hidden p-6" style="margin-bottom: 20px;">
                    <div class="mb-2 text-sm text-gray-500 font-semibold">
                        Danh mục: {{ $post->category && $post->category->parent ? $post->category->parent->name : ($post->category ? $post->category->name : 'Chưa phân loại') }}
                    </div>
                    <div class="flex justify-between mb-2 text-sm text-gray-600 font-semibold">
                        <div>Tên người dùng: {{ $post->author_name ?? 'Không rõ' }}</div>
                        <div>Ngày tạo: {{ $post->created_at->format('d/m/Y') }}</div>
                    </div>
                    <h2 class="text-2xl font-bold text-indigo-600 hover:underline text-center mb-4">
                        <a href="{{ route('posts.user_show', $post) }}" class="inline-block w-full">{{ $post->title }}</a>
                    </h2>
                    <p class="text-gray-700 mb-4">{{ $post->short_description }}</p>

                    @if($post->banner)
                        <img src="{{ asset('storage/' . $post->banner) }}" alt="{{ $post->title }}" class="w-24 h-24 object-cover rounded mx-auto">
                    @endif

                    <div class="mt-4 text-center">
                        <a href="{{ route('posts.user_show', $post) }}" class="inline-block px-4 py-2 border border-indigo-600 text-indigo-600 rounded hover:bg-indigo-700 hover:text-white transition">
                            Xem chi tiết
                        </a>
                    </div>
                </article>
            @empty
                <p class="text-center text-gray-500">Không có bài viết nào.</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $posts->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
