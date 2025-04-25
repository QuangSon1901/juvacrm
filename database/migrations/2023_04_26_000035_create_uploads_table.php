<?php
// 2023_04_26_000035_create_uploads_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('tbl_users');
            $table->string('type', 100)->nullable();
            $table->text('details')->nullable();
            $table->string('fk_key', 100)->nullable();
            $table->unsignedBigInteger('fk_value')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('name', 100)->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('driver_id', 255)->nullable();
            $table->string('extension', 100)->nullable();
            $table->string('action', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_uploads');
    }
}