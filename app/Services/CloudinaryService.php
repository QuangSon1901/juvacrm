<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    private $cloudinary;

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

        $this->cloudinary = new Cloudinary();
    }

    public function uploadFile($file)
    {
        $upload = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => 'uploads', // Thư mục lưu trữ trên Cloudinary
            'eager' => []
        ]);

        return [
            'url' => $upload['url'],
            'secure_url' => $upload['secure_url']
        ];
    }
}
