<?php
// 2023_04_26_000030_create_task_mission_assignments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskMissionAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_task_mission_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('mission_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('quantity_required')->default(0);
            $table->integer('quantity_completed')->default(0);
            $table->string('status', 50)->default('in_progress');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_task_mission_assignments');
    }
}