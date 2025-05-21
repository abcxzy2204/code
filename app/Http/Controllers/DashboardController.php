<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class DashboardController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get(); // Không còn điều kiện status
        return view('posts.partials.index', compact('posts'));
    }
}
