<?php
// 2023_04_26_000031_create_task_mission_reports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskMissionReportsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_task_mission_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('quantity');
            $table->text('note')->nullable();
            $table->timestamp('date_completed');
            $table->decimal('price', 15, 2)->default(0.00);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_task_mission_reports');
    }
}