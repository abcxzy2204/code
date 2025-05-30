<x-app-layout>
    <div class="flex justify-center p-6 min-h-screen" style="background: linear-gradient(to right, #a78bfa, #f9a8d4, #fcd34d);">
        <table class="min-w-[600px] bg-white rounded-lg shadow-md overflow-hidden">
            <thead class="bg-white text-black">
                <tr>
                    <th colspan="2" class="px-6 py-3 text-center text-lg font-semibold" style="background-color: #2451a3; color: #fff;">Chi tiết bài viết</th>
                </tr>
                <tr>
                    <td colspan="2" class="border-b-2 border-black"></td>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold w-1/3">Tiêu đề</td>
                    <td class="px-6 py-4">{{ $post->title }}</td>
                </tr>
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold w-1/3">Tên tác giả</td>
                    <td class="px-6 py-4">{{ $post->author_name ?? 'Không xác định' }}</td>
                </tr>
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold w-1/3">Mô tả ngắn</td>
                    <td class="px-6 py-4">{{ $post->short_description }}</td>
                </tr>
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold w-1/3">Nội dung</td>
                    <td class="px-6 py-4 prose max-w-full">{!! $post->content !!}</td>
                </tr>
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold w-1/3">Banner</td>
                    <td class="px-6 py-4">
                        @if($post->banner)
                            <img src="{{ asset('storage/' . $post->banner) }}" alt="Banner" class="rounded cursor-pointer" style="width: 500px; height: 300px; object-fit: cover;" onclick="openModal(this.src)">
                        @else
                            <span>Không có</span>
                        @endif
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold w-1/3">Ảnh bài viết</td>
                    <td class="px-6 py-4">
                        @if($post->images->count())
                            <div class="flex space-x-4 overflow-x-auto">
                                @foreach ($post->images as $image)
                                    <img src="{{ asset('storage/' . $image->image) }}" alt="Ảnh bài viết" class="h-16 object-cover rounded shadow cursor-pointer" onclick="openModal(this.src)">
                                @endforeach
                            </div>
                        @else
                            <span>Không có</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
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
