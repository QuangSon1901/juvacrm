<?php
// 2023_04_26_000020_create_consultation_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationLogsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_consultation_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consultation_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message')->nullable();
            $table->integer('action')->default(0)->comment('0 - hẹn tư vấn, 1 - đang tư vấn, 2 - đã tư vấn, 3 - huỷ hẹn, 4 - quá hạn');
            $table->dateTime('follow_up_date')->nullable()->comment('Ngày hẹn tư vấn tiếp theo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('consultation_id', 'idx_consultation_logs_consultation_id');
            $table->index('user_id', 'idx_consultation_logs_user_id');
            $table->index('action', 'idx_consultation_logs_action');
            $table->index('follow_up_date', 'idx_consultation_logs_follow_up_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_consultation_logs');
    }
}