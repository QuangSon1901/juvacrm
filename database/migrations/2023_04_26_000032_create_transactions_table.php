<?php
// 2023_04_26_000032_create_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('type')->nullable()->comment('0 - thu, 1 - chi');
            $table->unsignedBigInteger('category_id')->nullable()->comment('tbl_transaction_categories');
            $table->unsignedBigInteger('target_employee_id')->nullable()->comment('đối tượng nhân viên');
            $table->unsignedBigInteger('target_client_id')->nullable()->comment('đối tượng khách hàng');
            $table->string('target_other', 255)->nullable()->comment('đối tượng khác');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->dateTime('paid_date')->nullable();
            $table->integer('status')->nullable()->comment('0 - chờ, 1 - hoàn tất, 2 - đã huỷ');
            $table->string('note', 255)->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_transactions');
    }
}