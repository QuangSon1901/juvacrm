<?php
// 2023_04_26_000017_create_role_permissions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_role_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('level_id')->comment('ID chức vụ');
            $table->unsignedBigInteger('department_id')->comment('ID phòng ban');
            $table->unsignedBigInteger('permission_id')->comment('ID quyền');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            
            $table->unique(['level_id', 'department_id', 'permission_id'], 'level_department_permission');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_role_permissions');
    }
}