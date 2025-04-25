<?php
// 2023_04_26_000034_create_activity_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->comment('tbl_users');
            $table->string('action', 255);
            $table->string('ip', 100);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->text('details')->nullable();
            $table->string('fk_key', 100)->nullable();
            $table->string('fk_value', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_activity_logs');
    }
}