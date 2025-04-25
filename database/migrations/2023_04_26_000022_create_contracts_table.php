<?php
// 2023_04_26_000022_create_contracts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('contract_number', 255)->unique();
            $table->string('name', 255);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('tax_code', 50)->nullable();
            $table->string('company_address', 255)->nullable();
            $table->string('customer_representative', 255)->nullable();
            $table->string('customer_tax_code', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->dateTime('sign_date')->nullable();
            $table->dateTime('effective_date')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->integer('estimate_day')->nullable();
            $table->dateTime('estimate_date')->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->text('note')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->unsignedBigInteger('created_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->integer('is_active')->default(1);
            $table->integer('status')->default(0)->comment('0 - Đang chờ, 1 - Đang triển khai, 2 - Đã hoàn tất, 3 - Đã huỷ');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_contracts');
    }
}