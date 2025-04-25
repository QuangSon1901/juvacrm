<?php
// 2023_04_26_000028_create_task_feedback_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskFeedbackItemsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_task_feedback_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feedback_id');
            $table->integer('task_id');
            $table->boolean('is_resolved')->default(0);
            $table->integer('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolver_comment')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            
            $table->index('feedback_id', 'idx_feedback_id');
            $table->index('task_id', 'idx_task_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_task_feedback_items');
    }
}