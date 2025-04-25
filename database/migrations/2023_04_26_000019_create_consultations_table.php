<?php
// 2023_04_26_000019_create_consultations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_consultations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->string('name', 100)->nullable();
            $table->integer('is_deleted')->default(0);
            
            $table->index('customer_id', 'idx_consultations_customer_id');
            $table->index('is_deleted', 'idx_consultations_is_deleted');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_consultations');
    }
}