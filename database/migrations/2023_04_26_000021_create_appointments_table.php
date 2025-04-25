<?php
// 2023_04_26_000021_create_appointments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('note')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('color', ['success', 'warning', 'primary', 'gray', 'danger', 'neutral'])->default('primary');
            $table->integer('is_active')->default(1);
            $table->tinyInteger('is_completed')->default(0)->comment('0 - Chưa hoàn thành, 1 - Đã hoàn thành');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            
            $table->index('customer_id', 'idx_appointments_customer_id');
            $table->index('user_id', 'idx_appointments_user_id');
            $table->index('start_time', 'idx_appointments_start_time');
            $table->index(['is_active', 'is_completed'], 'idx_appointments_is_active_is_completed');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_appointments');
    }
}