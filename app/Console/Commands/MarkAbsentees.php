<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use App\Models\PartTimeSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkAbsentees extends Command
{
    protected $signature = 'attendance:mark-absent {date? : Ngày để đánh dấu vắng mặt (mặc định: hôm qua)}';
    protected $description = 'Đánh dấu những nhân viên vắng mặt không check-in theo từng ca làm việc';

    public function handle()
    {
        $targetDate = $this->argument('date') 
            ? Carbon::parse($this->argument('date'))->toDateString() 
            : Carbon::yesterday()->toDateString();
        
        $this->info("Đang đánh dấu vắng mặt cho ngày: $targetDate");
        Log::info("Starting MarkAbsentees command for date: $targetDate");
        
        // Lấy tất cả lịch làm việc đã duyệt cho ngày đó
        $schedules = PartTimeSchedule::where('schedule_date', $targetDate)
                                  ->where('status', 'approved')
                                  ->get();
        
        $markedCount = 0;
        
        foreach ($schedules as $schedule) {
            // Kiểm tra đã có bản ghi chấm công cho ca này chưa
            $hasAttendance = AttendanceRecord::where('schedule_id', $schedule->id)
                                         ->exists();
            
            // Chỉ đánh dấu vắng mặt nếu:
            // 1. Không có bản ghi chấm công nào cho ca này
            // 2. Thời gian kết thúc ca đã qua (tránh đánh dấu vắng mặt cho ca chưa diễn ra)
            $scheduleEndTime = Carbon::parse(date('Y-m-d', strtotime($targetDate)) . ' ' . date('H:i:s', strtotime($schedule->end_time)));
            
            if (!$hasAttendance && now()->gt($scheduleEndTime)) {
                AttendanceRecord::create([
                    'user_id' => $schedule->user_id,
                    'schedule_id' => $schedule->id,
                    'work_date' => $targetDate,
                    'status' => 'absent',
                    'total_hours' => 0,
                    'note' => 'Vắng mặt không báo trước - ' . formatDateTime(date('Y-m-d H:i:s', strtotime($schedule->start_time)), 'H:i') . ' đến ' . formatDateTime(date('Y-m-d H:i:s', strtotime($schedule->end_time)), 'H:i')
                ]);
                
                $markedCount++;
                Log::info("Marked absent for user #{$schedule->user_id}, schedule #{$schedule->id}, time slot: {$schedule->start_time}-{$schedule->end_time}");
            }
        }
        
        $this->info("Đã đánh dấu $markedCount ca vắng mặt.");
        Log::info("Completed MarkAbsentees command. Marked $markedCount shifts as absent.");
        
        return Command::SUCCESS;
    }
}