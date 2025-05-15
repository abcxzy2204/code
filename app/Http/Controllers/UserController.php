<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Hiển thị danh sách user
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
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
