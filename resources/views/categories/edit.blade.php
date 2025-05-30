<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow mt-10">
        <h1 class="text-2xl font-semibold mb-6">Sửa danh mục</h1>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="category" class="block font-semibold mb-2">Tên danh mục</label>
                <input type="text" name="category" id="category" value="{{ old('category', $category->name) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label for="parent_id" class="block font-semibold mb-2">Danh mục cha (tùy chọn)</label>
                <select name="parent_id" id="parent_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">-- Chọn danh mục cha --</option>
                    @foreach($categories as $parent)
                        <option value="{{ $parent->id }}" {{ (old('parent_id', $category->parent_id) == $parent->id) ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($category->children->count() > 0)
            <div class="mb-4">
                <label class="block font-semibold mb-2">Danh mục con</label>
                <ul>
                    @foreach($category->children as $child)
                    <li class="flex items-center justify-between mb-1">
                        <span>{{ $child->name }}</span>
                        <form action="{{ route('categories.destroyChild', $child->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục con này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">Xóa</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cập nhật danh mục</button>
        </form>
    </div>
</x-app-layout>
