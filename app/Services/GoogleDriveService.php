<?php

namespace App\Services;

use App\Models\Upload;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Illuminate\Support\Facades\Session;

class GoogleDriveService
{
    private $driveService;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName('Laravel Google Drive');
        $client->setScopes(config('google.scopes'));
        $client->setAuthConfig(config('google.credentials_json'));
        $client->setAccessType('offline');

        $this->driveService = new Google_Service_Drive($client);
    }

    /**
     * List files in Google Drive
     */
    public function listFiles()
    {
        // Thêm các trường cần thiết
        $params = [
            'fields' => 'files(id, name, mimeType, size, webContentLink)', // Chọn các trường cần thiết
            'pageSize' => 100 // Số lượng file tối đa trả về (tùy chỉnh)
        ];

        $files = $this->driveService->files->listFiles($params);
        return $files;
    }

    /**
     * Upload file to Google Drive
     */
    public function uploadFile($file, $action = null, $fk_key = null, $fk_value = null)
    {
        $driveServiceFile = new Google_Service_Drive_DriveFile();
        $driveServiceFile->setName($file->getClientOriginalName());

        $filePath = $file->getRealPath();

        $data = file_get_contents($filePath);

        $uploadedFile = $this->driveService->files->create(
            $driveServiceFile,
            [
                'data' => $data,
                'mimeType' => mime_content_type($filePath),
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]
        );

        $this->setFilePermissions($uploadedFile->id);

        Upload::create([
            'user_id'    => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            'type'       => $file->getClientMimeType(),
            'details'    => json_encode([
                'original_name' => $file->getClientOriginalName(),
                'extension'     => $file->getClientOriginalExtension(),
            ]),
            'name'       => $file->getClientOriginalName(),
            'size'       => $file->getSize(),
            'extension'  => $file->getClientOriginalExtension(),
            'driver_id'  => $uploadedFile->id,
            'action'  => $action,
            'fk_key' => $fk_key,
            'fk_value' => $fk_value,
        ]);

        return $uploadedFile;
    }

    private function setFilePermissions($fileId)
    {
        $permission = new Google_Service_Drive_Permission([
            'type' => 'anyone',
            'role' => 'reader',
        ]);

        return $this->driveService->permissions->create($fileId, $permission);
    }

    public function getStorageInfo()
    {
        return $this->driveService->about->get(['fields' => 'storageQuota']);
    }
}
