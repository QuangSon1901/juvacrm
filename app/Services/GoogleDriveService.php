<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;

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
    public function uploadFile($filePath)
    {
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($filePath->getClientOriginalName());

        $filePath = $filePath->getRealPath();

        $data = file_get_contents($filePath);

        $uploadedFile = $this->driveService->files->create(
            $file,
            [
                'data' => $data,
                'mimeType' => mime_content_type($filePath),
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]
        );

        $this->setFilePermissions($uploadedFile->id);

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
