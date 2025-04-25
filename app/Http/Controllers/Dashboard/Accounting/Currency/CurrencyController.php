<?php

namespace App\Http\Controllers\Dashboard\Accounting\Currency;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        return view("dashboard.accounting.currency.index");
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        // Xây dựng query cơ bản
        $query = Currency::query()
            ->when($request->input('filter.search'), function ($query, $search) {
                $query->where('currency_code', 'like', "%{$search}%")
                    ->orWhere('currency_name', 'like', "%{$search}%");
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
                'currency_code' => $item->currency_code,
                'currency_name' => $item->currency_name,
                'symbol' => $item->symbol,
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
            'content' => view('dashboard.accounting.currency.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'currency_code' => 'required|string|max:10|unique:tbl_currencies,currency_code',
            'currency_name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:10',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $currency = Currency::create([
                'currency_code' => $request->currency_code,
                'currency_name' => $request->currency_name,
                'symbol' => $request->symbol,
                'is_active' => $request->is_active,
            ]);

            LogService::saveLog([
                'action' => 'CREATE_CURRENCY',
                'ip' => $request->getClientIp(),
                'details' => "Đã tạo đơn vị tiền tệ: " . $request->currency_name,
                'fk_key' => 'tbl_currencies|id',
                'fk_value' => $currency->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Tạo đơn vị tiền tệ thành công',
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
            'id' => 'required|exists:tbl_currencies,id',
            'currency_code' => 'required|string|max:10|unique:tbl_currencies,currency_code,' . $request->id,
            'currency_name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:10',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $currency = Currency::findOrFail($request->id);
            
            $currency->update([
                'currency_code' => $request->currency_code,
                'currency_name' => $request->currency_name,
                'symbol' => $request->symbol,
                'is_active' => $request->is_active,
            ]);

            LogService::saveLog([
                'action' => 'UPDATE_CURRENCY',
                'ip' => $request->getClientIp(),
                'details' => "Đã cập nhật đơn vị tiền tệ: " . $request->currency_name,
                'fk_key' => 'tbl_currencies|id',
                'fk_value' => $currency->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật đơn vị tiền tệ thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
}