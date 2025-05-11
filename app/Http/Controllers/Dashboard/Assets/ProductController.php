<?php

namespace App\Http\Controllers\Dashboard\Assets;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view("dashboard.assets.product.index");
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);
        
        $productsQuery = Product::query();
        
        // Search functionality
        if (!empty($request['filter']['search'])) {
            $productsQuery->where('name', 'like', '%' . $request['filter']['search'] . '%');
        }
        
        // Status filter
        if (isset($request['filter']['status'])) {
            $productsQuery->where('is_active', $request['filter']['status']);
        }
        
        // Apply pagination
        $paginationResult = PaginationService::paginate($productsQuery, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];
        
        $result = $paginationResult['data']->map(function ($product, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $product->id,
                'name' => $product->name,
                'is_active' => $product->is_active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.assets.product.ajax-products', ['data' => $result, 'sorter' => $paginationResult['sorter']])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255|unique:tbl_products,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $product = Product::create([
                'name' => $request->name,
                'is_active' => 1
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Sản phẩm đã được tạo thành công!',
                'data' => [
                    'id' => $product->id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => PRODUCT_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã tạo sản phẩm mới: ' . $request->name,
                'fk_key' => 'tbl_products|id',
                'fk_value' => $response->data->id,
            ]);
        });
    }

    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_products,id',
            'name' => 'required|string|max:255|unique:tbl_products,name,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $product = Product::find($request->id);
            $product->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Sản phẩm đã được cập nhật thành công!',
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => PRODUCT_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã cập nhật sản phẩm: ' . $request->name,
                'fk_key' => 'tbl_products|id',
                'fk_value' => $request->id,
            ]);
        });
    }

    public function changeStatus(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_products,id',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $product = Product::find($request->id);
            $product->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Trạng thái sản phẩm đã được cập nhật thành công!',
            ]);
        }, function ($request, $response) {
            $status = $request->is_active ? 'kích hoạt' : 'vô hiệu hóa';
            LogService::saveLog([
                'action' => PRODUCT_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã ' . $status . ' sản phẩm #' . $request->id,
                'fk_key' => 'tbl_products|id',
                'fk_value' => $request->id,
            ]);
        });
    }
}