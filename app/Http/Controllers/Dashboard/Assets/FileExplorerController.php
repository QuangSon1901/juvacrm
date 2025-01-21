<?php

namespace App\Http\Controllers\Dashboard\Assets;

use App\Http\Controllers\Controller;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

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
        
        return view("dashboard.assets.file_explorer.index", ['storage' => $storageResult]);
    }
}
