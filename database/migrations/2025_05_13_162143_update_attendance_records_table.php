<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendanceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_attendance_records', function (Blueprint $table) {
            $table->decimal('late_minutes', 8, 2)->default(0)->after('total_hours');
            $table->decimal('early_leave_minutes', 8, 2)->default(0)->after('late_minutes');
            $table->decimal('overtime_hours', 8, 2)->default(0)->after('early_leave_minutes');
            $table->decimal('valid_hours', 8, 2)->default(0)->after('overtime_hours');
            $table->text('late_reason')->nullable()->after('note');
            $table->text('early_leave_reason')->nullable()->after('late_reason');
            $table->boolean('forgot_checkout')->default(false)->after('early_leave_reason');
            $table->text('forgot_checkout_reason')->nullable()->after('forgot_checkout');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_attendance_records', function (Blueprint $table) {
            $table->dropColumn([
                'late_minutes',
                'early_leave_minutes',
                'overtime_hours',
                'valid_hours',
                'late_reason',
                'early_leave_reason',
                'forgot_checkout',
                'forgot_checkout_reason',
            ]);
        });
    }
}
