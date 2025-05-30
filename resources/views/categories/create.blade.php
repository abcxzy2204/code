<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow mt-10">
        <h1 class="text-2xl font-semibold mb-6">Tạo danh mục mới</h1>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="category" class="block font-semibold mb-2">Tên danh mục</label>
                <input type="text" name="category" id="category" value="{{ old('category') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            @if($parentId)
            <div class="mb-4">
                <label for="parent_id" class="block font-semibold mb-2">Danh mục cha (tùy chọn)</label>
                <select name="parent_id" id="parent_id" class="w-full border border-gray-300 rounded px-3 py-2" readonly>
                    <option value="{{ $parentId }}" selected>{{ $categories->firstWhere('id', $parentId)->name ?? '' }}</option>
                </select>
            </div>
            @endif
            <button type="submit" class="px-4 py-2 bg-blue-600 text-black border border-gray-300 ">Tạo danh mục</button>
        </form>

        <div class="mt-6 border border-gray-300 rounded p-4" style="display: inline-block;">
            <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-gray-600 text-black ">Quản lý danh mục (Xóa, Sửa)</a>
        </div>
    </div>
</x-app-layout>
