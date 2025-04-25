<?php
// 2023_04_26_000002_create_departments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('keyword', ['OWNER', 'TECHNICAL', 'SALE', 'ACCOUNTING'])->nullable();
            $table->string('name', 255);
            $table->string('note', 255)->nullable();
            $table->boolean('is_active')->default(1)->comment('0 - block, 1 - active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_departments');
    }
}