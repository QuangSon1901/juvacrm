<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function detail(Request $request)
    {
        $date = formatDateTime(trim($request['datetime']) . ' 00:00:00') != ''
            ? Carbon::parse(formatDateTime(trim($request['datetime']) . ' 00:00:00'))
            : Carbon::now();

        $appointments_day = Appointment::select('id', 'name', 'note', 'start_time', 'end_time', 'color', 'user_id', 'customer_id', 'created_at')
            ->whereDate('start_time', $date->toDateString())
            ->get()
            ->toArray();

        // Tạo bản sao của $date để không làm thay đổi giá trị gốc
        $startOfWeek = $date->copy()->startOfWeek();  // Ngày đầu tuần
        $weekDays = [];
        $daysOfWeek = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = [
                'date' => $startOfWeek->day,  // Thêm 1 ngày cho mỗi vòng lặp
                'day' => $daysOfWeek[$i],
            ];

            $startOfWeek->addDay();
        }

        $appointments_week = Appointment::select('id', 'name', 'note', 'start_time', 'end_time', 'color', 'user_id', 'customer_id', 'created_at')
            ->whereBetween('start_time', [$date->copy()->startOfWeek()->toDateString(), $date->copy()->endOfWeek()->toDateString()]) // Lọc theo tuần
            ->get()
            ->toArray();

        $appointments_month = Appointment::select('id', 'name', 'note', 'start_time', 'end_time', 'color', 'user_id', 'customer_id', 'created_at')
            ->whereMonth('start_time', $date->month)
            ->whereYear('start_time', $date->year)
            ->get()
            ->toArray();

        $type = $request['type'] ?? 'day';
        switch ($type) {
            case 'day':
                $currentDateFormat = $date->translatedFormat('l, d') . ' tháng ' . $date->translatedFormat('m, Y');
                break;
            case 'week':
                $currentDateFormat = 'Tuần ' . $date->weekOfYear . ', tháng ' . $date->translatedFormat('m, Y');
                break;
            case 'month':
                $currentDateFormat = 'Tháng ' . $date->translatedFormat('m, Y');
                break;
            default:
                $currentDateFormat = $date->translatedFormat('l, d') . 'Tháng ' . $date->translatedFormat('m, Y');
                break;
        }

        return view('dashboard.customer.support.appointment.detail', [
            'currentDateFormat' => $currentDateFormat,
            'currentDate' => $date->translatedFormat('Y-m-d'),
            'appointments' => [
                'day' => $appointments_day,
                'week' => $appointments_week,
                'month' => $appointments_month,
                'week_days' => $weekDays,
            ],
            'type' => $type,
        ]);
    }

    public function dataAppointment()
    {
        $appointments = Appointment::select('id', 'name', 'note', 'start_time', 'end_time', 'color', 'user_id', 'customer_id', 'created_at')->get()->toArray();

        return response()->json([
            'status' => 200,
            'content' => [
                'day' => view('dashboard.customer.support.appointment.calendar_day', ['appointments' => $appointments])->render(),
                'week' => view('dashboard.customer.support.appointment.calendar_week', ['appointments' => $appointments])->render(),
                'month' => view('dashboard.customer.support.appointment.calendar_month', ['appointments' => $appointments])->render(),
            ],
        ]);
    }
}
