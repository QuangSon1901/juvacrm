<?php

namespace App\Http\Controllers\Dashboard\Accounting\Category;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        return view("dashboard.accounting.category.index");
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        // Xây dựng query cơ bản
        $query = TransactionCategory::query()
            ->when($request->input('filter.search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->has('filter.type') && $request->input('filter.type') !== null, function ($query) use ($request) {
                $query->where('type', $request->input('filter.type'));
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
                'type' => $item->type,
                'type_text' => $item->type == 0 ? 'Thu' : 'Chi',
                'note' => $item->note,
                'is_active' => $item->is_active,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.accounting.category.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:100',
            'type' => 'required|in:0,1',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $category = TransactionCategory::create([
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'note' => $request->input('note'),
                'is_active' => 1,
            ]);

            LogService::saveLog([
                'action' => 'CREATE_TRANSACTION_CATEGORY',
                'ip' => $request->getClientIp(),
                'details' => "Đã tạo danh mục " . ($request->input('type') == 0 ? 'thu' : 'chi') . ": " . $request->input('name'),
                'fk_key' => 'tbl_transaction_categories|id',
                'fk_value' => $category->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Tạo danh mục thành công',
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
            'id' => 'required|exists:tbl_transaction_categories,id',
            'name' => 'nullable|string|max:100',
            'type' => 'nullable|in:0,1',
            'note' => 'nullable|string|max:255',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $category = TransactionCategory::findOrFail($request->input('id'));
            $data = array_filter($request->only(['name', 'type', 'note', 'is_active']), function ($value) {
                return $value !== null;
            });

            $category->update($data);

            LogService::saveLog([
                'action' => 'UPDATE_TRANSACTION_CATEGORY',
                'ip' => $request->getClientIp(),
                'details' => "Đã cập nhật danh mục: " . $category->name,
                'fk_key' => 'tbl_transaction_categories|id',
                'fk_value' => $category->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật danh mục thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
}