<?php
// 2023_04_26_000004_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('birth_date', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->integer('gender')->default(0)->comment('0 - male, 1 - female, 2 - other');
            $table->string('cccd', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('username', 100);
            $table->string('password', 100);
            $table->decimal('salary', 10, 0)->nullable();
            $table->integer('status')->default(1)->comment('0 - not working, 1 - working');
            $table->integer('is_active')->default(1)->comment('0 - block, 1 - active');
            $table->timestamp('last_login')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamp('login_attempts')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('created_by')->nullable()->comment('0 - Hệ thống\r\n1 - User');
            $table->boolean('is_super_admin')->default(0)->comment('Tài khoản có toàn quyền');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_users');
    }
}