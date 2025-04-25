<?php
// 2023_04_26_000005_create_customer_class_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerClassTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_customer_class', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('is_active')->nullable()->comment('0 - block, 1 - active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->enum('color', ['success', 'warning', 'primary', 'gray', 'danger', 'neutral'])->default('neutral');
            $table->integer('sort')->default(1);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_customer_class');
    }
}