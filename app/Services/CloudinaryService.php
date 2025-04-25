<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    protected $uploadApi;


    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        $this->uploadApi = new UploadApi();
    }

    public function uploadFile($file)
    {
        $fileType = $file->getClientMimeType();
        $extension = $file->getClientOriginalExtension();
        
        // Xác định resource_type dựa trên MIME type
        $resourceType = 'auto'; // Auto sẽ tự phát hiện loại file
        
        // Có thể thêm các định dạng đặc biệt nếu cần
        if (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar'])) {
            $resourceType = 'raw';
        }
        
        $response = $this->uploadApi->upload($file->getRealPath(), [
            'folder' => 'uploads',
            'resource_type' => $resourceType,
            'use_filename' => true,
            'unique_filename' => true,
        ]);

        return [
            'url' => $response['url'],
            'secure_url' => $response['secure_url'],
            'public_id' => $response['public_id'],
        ];
    }
}
