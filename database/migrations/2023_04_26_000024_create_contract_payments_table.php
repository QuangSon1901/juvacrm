<?php
// 2023_04_26_000024_create_contract_payments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_contract_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('method_id')->nullable();
            $table->integer('payment_stage')->nullable()->comment('0 - deposit, 1 - bonus, 2 - final, 3 - deduction');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('status')->default(0)->comment('0 - pending, 1 - completed');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('paid_date')->nullable();
            $table->string('note', 255)->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->string('name', 100)->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('created_id')->nullable();
            $table->integer('is_active')->default(1);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_contract_payments');
    }
}