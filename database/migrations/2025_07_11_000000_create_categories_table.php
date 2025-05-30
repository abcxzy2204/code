<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Optional: migrate existing distinct categories from posts to categories table
        $categories = DB::table('posts')->select('category')->distinct()->whereNotNull('category')->where('category', '!=', '')->get();
        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category->category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add category_id column to posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('short_description');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        // Optional: update posts.category_id based on posts.category string
        $posts = DB::table('posts')->get();
        foreach ($posts as $post) {
            if ($post->category) {
                $category = DB::table('categories')->where('name', $post->category)->first();
                if ($category) {
                    DB::table('posts')->where('id', $post->id)->update(['category_id' => $category->id]);
                }
            }
        }

        // Drop old category column
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back category string column
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->nullable()->after('short_description');
        });

        // Optional: migrate category_id back to category string
        $posts = DB::table('posts')->get();
        foreach ($posts as $post) {
            if ($post->category_id) {
                $category = DB::table('categories')->where('id', $post->category_id)->first();
                if ($category) {
                    DB::table('posts')->where('id', $post->id)->update(['category' => $category->name]);
                }
            }
        }

        // Drop category_id foreign key and column
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
}
