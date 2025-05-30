<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('short_description');
        $table->longText('content'); // WYSIWYG
        $table->string('banner')->nullable(); // Banner image path
        $table->string('category'); // <-- thêm dòng này
        $table->unsignedBigInteger('user_id'); // <-- thêm dòng này

        // Nếu có bảng users, thêm foreign key (không bắt buộc nhưng nên có)
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
