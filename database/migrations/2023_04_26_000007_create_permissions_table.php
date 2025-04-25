<?php
// 2023_04_26_000007_create_permissions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->comment('Tên quyền');
            $table->string('slug', 255)->unique()->comment('Định danh quyền');
            $table->string('description', 255)->nullable();
            $table->string('module', 100)->comment('Module liên quan');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_permissions');
    }
}