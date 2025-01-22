<?php

namespace App\Http\Controllers\Dashboard\Assets;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Services\GoogleDriveService;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Session;

class FileExplorerController extends Controller
{
    public function index()
    {
        $googleDriveService = new GoogleDriveService();
        $storageInfo = $googleDriveService->getStorageInfo();

        $storageResult = [
            'limit' => $storageInfo->storageQuota->limit,
            'usage' => $storageInfo->storageQuota->usage,
            'remaining' => $storageInfo->storageQuota->limit - $storageInfo->storageQuota->usage,
        ];

        $query = Upload::query()->where('action', MEDIA_DRIVER_UPLOAD);

        $paginationResult = PaginationService::paginate($query, 1, CARD_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'name' => $item->name,
                'driver_id' => $item->driver_id,
                'type' => $item->type,
                'size' => $item->size,
                'extension' => $item->extension,
                'details' => $item->details,
                'user' => [
                    'id' => $item->user->id ?? 0,
                    'name' => $item->user->name ?? '',
                ],
                'created_at' => $item->created_at,
            ];
        });
        
        return view("dashboard.assets.file_explorer.index", [
            'storage' => $storageResult, 
            'data' => $result,
            'count_all' => $query->count(),
            'count_owner' => $query->where('user_id', Session::get(ACCOUNT_CURRENT_SESSION)['id'])->count(),
        ]);
    }
}
