<?php
// 2023_04_26_000029_create_task_contributions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskContributionsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_task_contributions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->dateTime('date_completed')->nullable();
            $table->text('note')->nullable();
            $table->integer('is_active')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_task_contributions');
    }
}