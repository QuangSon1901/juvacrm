<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GoogleDriveController extends Controller
{
    private $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    /**
     * List all files in Google Drive.
     */
    public function listFiles()
    {
        $files = $this->googleDriveService->listFiles();
        return view('google.files', compact('files'));
    }

    public function uploadFile(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,svg,pdf,txt,doc,docx,xls,xlsx,csv,ppt,pptx,zip,rar'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $file = $request->file('file');
            $uploadedFile = $this->googleDriveService->uploadFile($file, $request['action'] ?? MEDIA_DRIVER_UPLOAD);

            return response()->json([
                'status' => 200,
                'message' => 'Tải lên tệp thành công.',
                'data' => [
                    'driver_id' => $uploadedFile->id,
                    'extension' => $file->getClientOriginalExtension(),
                    'type' => $file->getClientMimeType(),
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => MEDIA_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Tải lên tệp',
                'fk_key' => 'tbl_uploads|driver_id',
                'fk_value' => $response->data->driver_id,
            ]);
        });
    }

    public function getStorageInfo(Request $request)
    {
        $storageInfo = $this->googleDriveService->getStorageInfo();

        return [
            'limit' => formatBytes($storageInfo->storageQuota->limit), // Tổng dung lượng
            'usage' => formatBytes($storageInfo->storageQuota->usage), // Dung lượng đã sử dụng
            'remaining' => formatBytes($storageInfo->storageQuota->limit - $storageInfo->storageQuota->usage), // Dung lượng còn lại
        ];
    }
}
