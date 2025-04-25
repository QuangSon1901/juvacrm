<?php
// 2023_04_26_000011_create_system_config_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemConfigTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_system_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('config_key', 100);
            $table->string('config_value', 100);
            $table->string('description', 255)->nullable();
            $table->integer('is_active')->comment('0 - block, 1 - active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_system_config');
    }
}