<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;

class UserController extends Controller
{
    // Hiển thị danh sách user
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // Trang chủ cho user
    public function userHome(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');

        $posts = Post::query();

        if ($search) {
            $posts = $posts->where(function($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                      ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        if ($categoryId) {
            // Filter posts by category or its children
            $posts = $posts->whereHas('category', function($query) use ($categoryId) {
                $query->where('id', $categoryId)
                      ->orWhere('parent_id', $categoryId);
            });
        }

        $posts = $posts->orderBy('created_at', 'desc')->paginate(10);

        return view('user_home', compact('posts', 'search'));
    }

    // Trang danh mục dành cho user
    public function userDanhMuc()
    {
        // Lấy danh sách danh mục cha cùng với danh mục con
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();

        return view('user_danhmuc', compact('categories'));
    }

    // Hiển thị form tạo user mới
    public function create()
    {
        return view('users.create');
    }

    // Lưu user mới vào database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // Hiển thị thông tin 1 user
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // Hiển thị form sửa user
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Cập nhật thông tin user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Xóa user
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
