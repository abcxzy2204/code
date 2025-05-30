<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // Show list of categories with children
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('categories.index', compact('categories'));
    }

    // Show form to create new category
    public function create(Request $request)
    {
        $categories = Category::whereNull('parent_id')->get();
        $parentId = $request->query('parent_id', null);
        return view('categories.create', compact('categories', 'parentId'));
    }

    // Store new category or subcategory
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Category::create([
            'name' => $request->category,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('danhmuc')->with('success', 'Danh mục mới đã được tạo thành công!');
    }

    // Show form to edit category
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('categories.edit', compact('category', 'categories'));
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:255|unique:categories,name,' . $id,
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category->update([
            'name' => $request->category,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('danhmuc')->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    // Delete a category and update children parent_id to null
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Update posts with this category_id to null
        DB::table('posts')->where('category_id', $category->id)->update(['category_id' => null]);

        // Update children parent_id to null
        Category::where('parent_id', $category->id)->update(['parent_id' => null]);

        $category->delete();

        return redirect()->route('danhmuc')->with('success', 'Danh mục đã được xóa thành công!');
    }

    // Delete a child category by id (AJAX or form)
    public function destroyChild($id)
    {
        $category = Category::findOrFail($id);

        // Update posts with this category_id to null
        DB::table('posts')->where('category_id', $category->id)->update(['category_id' => null]);

        // Update children parent_id to null
        Category::where('parent_id', $category->id)->update(['parent_id' => null]);

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Danh mục con đã được xóa thành công!']);
    }
}
