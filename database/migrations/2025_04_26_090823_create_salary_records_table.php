<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_salary_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('period_month');
            $table->integer('period_year');
            $table->decimal('base_salary', 15, 2);
            $table->decimal('attendance_bonus', 15, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('insurance_amount', 15, 2)->default(0);
            $table->decimal('advance_payments', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2);
            $table->enum('status', ['pending', 'processed', 'paid'])->default('pending');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_salary_records');
    }
}