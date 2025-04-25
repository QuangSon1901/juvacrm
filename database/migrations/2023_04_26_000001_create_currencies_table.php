<?php
// 2023_04_26_000001_create_currencies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_currencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('currency_code', 10);
            $table->string('currency_name', 100);
            $table->string('symbol', 10)->nullable();
            $table->integer('is_active')->default(1)->comment('0 - inactive, 1 - active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_currencies');
    }
}