<?php

namespace App\Http\Controllers\Dashboard\Accounting\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return view("dashboard.accounting.payment_method.index");
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        // Xây dựng query cơ bản
        $query = PaymentMethod::query()
            ->when($request->input('filter.search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->has('filter.is_active') && $request->input('filter.is_active') !== null, function ($query) use ($request) {
                $query->where('is_active', $request->input('filter.is_active'));
            });

        // Phân trang
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        // Format dữ liệu trả về
        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'is_active' => $item->is_active,
                'status_text' => $item->is_active == 1 ? 'Hoạt động' : 'Không hoạt động',
                'created_at' => $item->created_at,
                'created_at_formatted' => formatDateTime($item->created_at, 'd/m/Y H:i'),
                'updated_at' => $item->updated_at,
                'updated_at_formatted' => formatDateTime($item->updated_at, 'd/m/Y H:i'),
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.accounting.payment_method.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $paymentMethod = PaymentMethod::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active,
            ]);

            LogService::saveLog([
                'action' => 'CREATE_PAYMENT_METHOD',
                'ip' => $request->getClientIp(),
                'details' => "Đã tạo phương thức thanh toán: " . $request->name,
                'fk_key' => 'tbl_payment_methods|id',
                'fk_value' => $paymentMethod->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Tạo phương thức thanh toán thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_payment_methods,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $paymentMethod = PaymentMethod::findOrFail($request->id);
            
            $paymentMethod->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active,
            ]);

            LogService::saveLog([
                'action' => 'UPDATE_PAYMENT_METHOD',
                'ip' => $request->getClientIp(),
                'details' => "Đã cập nhật phương thức thanh toán: " . $request->name,
                'fk_key' => 'tbl_payment_methods|id',
                'fk_value' => $paymentMethod->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật phương thức thanh toán thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
}