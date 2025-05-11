<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Customer;
use App\Services\LogService;
use App\Services\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AppointmentController extends Controller
{
    public function detail(Request $request)
    {
        // Xử lý tham số ngày và khách hàng
        $date = formatDateTime(trim($request['date']) . ' 00:00:00') != ''
            ? Carbon::parse(formatDateTime(trim($request['date']) . ' 00:00:00'))
            : Carbon::now();
        
        $selectedCustomerId = $request['customer_id'] ?? null;
        
        // Lấy danh sách khách hàng
        $customers = Customer::where('is_active', 1)->orderBy('name')->get();
        
        // Lấy tất cả lịch hẹn trong tháng hiện tại
        $appointmentsQuery = Appointment::select('id', 'name', 'note', 'start_time', 'end_time', 'color', 'user_id', 'customer_id', 'is_completed', 'created_at')
            ->whereMonth('start_time', $date->month)
            ->whereYear('start_time', $date->year);
        
        // Lọc theo khách hàng nếu có
        if ($selectedCustomerId) {
            $appointmentsQuery->where('customer_id', $selectedCustomerId);
        }
        
        $appointments = $appointmentsQuery->with('customer')->get();
        
        // Đếm số lịch hẹn theo trạng thái
        $upcomingCount = Appointment::where('start_time', '>=', now());
        $todayCount = Appointment::whereDate('start_time', today());
        $pastCount = Appointment::where('start_time', '<', now());
        
        // Lọc theo khách hàng nếu có
        if ($selectedCustomerId) {
            $upcomingCount->where('customer_id', $selectedCustomerId);
            $todayCount->where('customer_id', $selectedCustomerId);
            $pastCount->where('customer_id', $selectedCustomerId);
        }
        
        // Định dạng hiển thị tháng
        $currentDateFormat = 'Tháng ' . $date->translatedFormat('m, Y');
        
        return view('dashboard.customer.support.appointment.detail', [
            'currentDateFormat' => $currentDateFormat,
            'currentDate' => $date->format('Y-m-d'),
            'appointments' => $appointments,
            'customers' => $customers,
            'selectedCustomerId' => $selectedCustomerId,
            'statistics' => [
                'upcoming' => $upcomingCount->count(),
                'today' => $todayCount->count(),
                'past' => $pastCount->count()
            ]
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

    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'note' => 'nullable|string',
            'color' => 'required|string|in:success,warning,primary,gray,danger,neutral',
            'customer_id' => 'required|exists:tbl_customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $data = $request->only('name', 'start_time', 'end_time', 'note', 'color', 'customer_id');
            $data['user_id'] = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            
            $appointment = Appointment::create($data);
            
            // Cập nhật lần tương tác cuối với khách hàng
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $customer->updateLastInteraction();
            }

            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã tạo lịch hẹn mới: ' . $request->name,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $request->customer_id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Tạo lịch hẹn thành công.',
                'data' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }
    
    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_appointments,id',
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'note' => 'nullable|string',
            'color' => 'required|string|in:success,warning,primary,gray,danger,neutral',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $appointment = Appointment::find($request->id);
            if (!$appointment) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lịch hẹn không tồn tại.',
                ]);
            }
            
            $appointment->update($request->only('name', 'start_time', 'end_time', 'note', 'color'));

            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã cập nhật lịch hẹn: ' . $request->name,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $appointment->customer_id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật lịch hẹn thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }
    
    public function delete(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_appointments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $appointment = Appointment::find($request->id);
            if (!$appointment) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lịch hẹn không tồn tại.',
                ]);
            }
            
            $customer_id = $appointment->customer_id;
            $appointment_name = $appointment->name;
            
            $appointment->delete();

            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã hủy lịch hẹn: ' . $appointment_name,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $customer_id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Hủy lịch hẹn thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }
    
    public function getCustomerAppointments(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'customer_id' => 'required|exists:tbl_customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $appointments = Appointment::where('customer_id', $request->customer_id)
                ->orderBy('start_time', 'desc')
                ->get();
                
            $customer = Customer::find($request->customer_id);

            return response()->json([
                'status' => 200,
                'data' => [
                    'appointments' => $appointments,
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                        'email' => $customer->email
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }

    public function completeAppointment(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_appointments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $appointment = Appointment::find($request->id);
            if (!$appointment) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lịch hẹn không tồn tại.',
                ]);
            }
            
            $appointment->is_completed = 1;
            $appointment->save();
            
            // Cập nhật lần tương tác cho khách hàng
            if ($appointment->customer) {
                $appointment->customer->updateLastInteraction();
            }

            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã đánh dấu hoàn thành lịch hẹn: ' . $appointment->name,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $appointment->customer_id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Đã đánh dấu hoàn thành lịch hẹn thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }
}
