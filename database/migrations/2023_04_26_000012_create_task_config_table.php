<?php
// 2023_04_26_000012_create_task_config_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskConfigTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_task_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('type')->nullable()->comment('0 - priority, 1 - status, 2 - issue');
            $table->integer('is_active')->default(1)->comment('0 - block, 1 - active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->enum('color', ['success', 'warning', 'primary', 'gray', 'danger', 'neutral', 'info'])->default('neutral');
            $table->integer('sort')->default(1);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_task_config');
    }
}