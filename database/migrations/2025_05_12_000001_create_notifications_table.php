<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Người nhận thông báo');
            $table->unsignedBigInteger('sender_id')->nullable()->comment('Người gửi thông báo');
            $table->string('title', 255);
            $table->text('content');
            $table->string('type', 50)->comment('Loại thông báo');
            $table->string('action_url', 255)->nullable()->comment('URL khi click vào thông báo');
            $table->string('icon', 50)->default('ki-notification')->comment('Icon hiển thị');
            $table->string('icon_color', 20)->default('primary')->comment('Màu sắc của icon');
            $table->tinyInteger('importance')->default(5)->comment('Mức độ quan trọng (1-10)');
            $table->boolean('is_read')->default(false)->comment('Đã đọc hay chưa');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_notifications');
    }
}