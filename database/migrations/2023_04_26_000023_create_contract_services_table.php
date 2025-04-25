<?php
// 2023_04_26_000023_create_contract_services_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractServicesTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_contract_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->enum('type', ['service', 'sub_service', 'discount', 'custom'])->nullable();
            $table->integer('quantity');
            $table->decimal('price', 15, 0);
            $table->string('note', 255)->nullable();
            $table->text('sample_image_id')->nullable();
            $table->text('result_image_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->integer('is_active')->default(1);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('service_type', 100)->default('"individual"');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_contract_services');
    }
}