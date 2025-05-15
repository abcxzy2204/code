<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 bg-white rounded shadow" style="margin-top: 30px;">
        <h1 class="text-3xl font-semibold mb-6">Danh sách bài viết</h1>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('posts.index') }}" class="mb-4 flex space-x-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm bài viết..."
                class="flex-grow border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200" />
            <button type="submit" style="border: 2px solid gray; border-radius: 0.375rem; background-color: gray; color: black; padding: 0.5rem 1rem;">
Tìm kiếm
</button>
        </form>

        <div class="flex justify-end space-x-8 mb-4">
            <a style=" border: 1px solid gray; border-radius: 0.375rem; background-color: blue; color: black; padding: 0.5rem 1rem;" href="{{ route('posts.create') }}" class="inline-block px-4 py-2 bg-indigo-600 text-black rounded hover:bg-indigo-700" >Thêm bài viết mới</a>
            <a href="{{ route('posts.export') }}" 
   style="border: 1px solid gray; border-radius: 0.375rem; background-color: green; color: black; padding: 0.5rem 1rem;" 
   class="inline-block px-4 py-2 bg-green-600 text-black rounded hover:bg-green-700 hover:text-white">
   Xuất file Excel
</a>
        </div>

        @if($posts->count())
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-left">Tiêu đề</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Mô tả ngắn</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Ngày tạo</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $post->title }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ Str::limit($post->short_description, 50) }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $post->created_at->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <div class="flex justify-center space-x-6 items-center max-w-xs mx-auto">
                                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:underline px-2 py-1 border border-blue-600 rounded text-center">Xem</a>
                                <a href="{{ route('posts.edit', $post) }}" class="text-yellow-600 hover:underline px-2 py-1 border border-yellow-600 rounded text-center">Sửa</a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline px-2 py-1 border border-red-600 rounded text-center">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <p>Chưa có bài viết nào.</p>
        @endif
    </div>
</x-app-layout>
