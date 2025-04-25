<?php
// 2023_04_26_000033_create_contract_commissions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractCommissionsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_contract_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('commission_percentage', 5, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->decimal('contract_value', 15, 2);
            $table->timestamp('processed_at')->nullable();
            $table->integer('is_paid')->default(0)->comment('0 - Chưa chi, 1 - Đã chi');
            $table->unsignedBigInteger('transaction_id')->nullable()->comment('ID phiếu chi nếu đã chi');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_contract_commissions');
    }
}