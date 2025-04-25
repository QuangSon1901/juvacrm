<?php
// 2023_04_26_000014_create_transaction_categories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_transaction_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('type')->nullable()->comment('0 - thu, 1 - chi');
            $table->string('name', 100);
            $table->string('note', 255)->nullable();
            $table->integer('is_active')->default(1)->comment('0 - block, 1 - active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_transaction_categories');
    }
}