<?php
// 2023_04_26_000025_create_tasks_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->integer('progress')->default(0);
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable()->comment('tbl_task_config type=0');
            $table->unsignedBigInteger('issue_id')->nullable()->comment('tbl_task_config type=2');
            $table->integer('estimate_time')->nullable();
            $table->integer('spend_time')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->unsignedBigInteger('status_id')->nullable()->comment('tbl_task_config type=1');
            $table->integer('is_active')->default(1)->comment('0 - block, 1 - active');
            $table->integer('qty_request')->default(0);
            $table->integer('qty_completed')->default(0);
            $table->enum('type', ['CONTRACT', 'SERVICE', 'SUB'])->nullable();
            $table->integer('is_updated')->nullable();
            $table->unsignedBigInteger('contract_service_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('original_task_id')->nullable();
            $table->unsignedBigInteger('assign_id')->nullable();
            $table->string('sub_name', 255)->nullable();
            $table->unsignedBigInteger('created_id')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->string('service_other', 255)->nullable();
            $table->decimal('bonus_amount', 10, 0)->default(0);
            $table->decimal('deduction_amount', 10, 0)->default(0);
            $table->boolean('has_feedback')->default(0);
            $table->boolean('feedback_resolved')->default(0);
            $table->string('sample_image_id', 255)->nullable();
            $table->string('result_image_id', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_tasks');
    }
}