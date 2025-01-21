<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

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

    /**
     * Upload a file to Google Drive.
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $filePath = $request->file('file');
        $uploadedFile = $this->googleDriveService->uploadFile($filePath);

        return redirect()->route('google.drive.files')->with('success', 'File uploaded successfully!');
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

    /**
     * Chuyển đổi byte thành định dạng dễ đọc (MB, GB, TB, ...)
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
