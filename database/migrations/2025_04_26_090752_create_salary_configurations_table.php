<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_salary_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // null cho cấu hình toàn hệ thống
            $table->enum('type', ['fulltime', 'part-time']);
            $table->decimal('hourly_rate', 15, 2)->nullable(); // cho part-time
            $table->decimal('monthly_salary', 15, 2)->nullable(); // cho fulltime
            $table->decimal('overtime_rate', 5, 2)->default(1.5);
            $table->decimal('attendance_bonus_rate', 5, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('insurance_rate', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_salary_configurations');
    }
}