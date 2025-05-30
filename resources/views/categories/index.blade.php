<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow mt-10">
        <h1 class="text-2xl font-semibold mb-6">Quản lý danh mục</h1>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($categories->isEmpty())
            <p>Chưa có danh mục nào.</p>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-2 text-left w-3/4">Tên danh mục</th>
                        <th class="border border-gray-300 p-2 text-center w-1/4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
@foreach($categories as $category)
    <tr class="bg-gray-100">
        <td class="border border-gray-300 p-2 w-3/4 font-semibold">{{ $category->name }}</td>
        <td class="border border-gray-300 p-2 text-center w-1/4">
            <a href="{{ route('categories.edit', $category->id) }}" class="text-blue-600 hover:underline mr-2">Sửa</a>
            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="delete-category-form inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Xóa</button>
            </form>
        </td>
    </tr>
    @foreach($category->children as $child)
    <tr>
        <td class="border border-gray-300 p-2 w-3/4 pl-8">- {{ $child->name }}</td>
        <td class="border border-gray-300 p-2 text-center w-1/4">
            <a href="{{ route('categories.edit', $child->id) }}" class="text-blue-600 hover:underline mr-2">Sửa</a>
            <form action="{{ route('categories.destroy', $child->id) }}" method="POST" class="delete-category-form inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Xóa</button>
            </form>
        </td>
    </tr>
    @endforeach
@endforeach
                </tbody>
            </table>
        @endif

        <div class="mt-6 flex space-x-4">
            <a href="{{ route('categories.create') }}" class="px-4 py-2 bg-blue-600 text-black rounded border border-blue-700 hover:bg-blue-700">Tạo danh mục cha</a>
            @if($categories->isNotEmpty())
            <a href="{{ route('categories.create', ['parent_id' => $categories->first()->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded border border-green-700 hover:bg-green-700">Tạo danh mục con</a>
            @else
            <button disabled class="px-4 py-2 bg-gray-400 text-white rounded border border-gray-500 cursor-not-allowed" title="Chưa có danh mục cha để tạo danh mục con">Tạo danh mục con</button>
            @endif
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $(document).on('submit', '.delete-category-form', function(e) {
                e.preventDefault(); // chặn submit form mặc định

                const form = this;

                Swal.fire({
                    title: 'Bạn có chắc muốn xóa danh mục này?',
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
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif
</x-app-layout>
