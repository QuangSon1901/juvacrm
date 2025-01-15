<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

class BizFlyStorageService
{
    protected $s3Client;

    public function __construct()
    {
        $accessKey = env('BIZFLY_ACCESS_KEY');
        $secretKey = env('BIZFLY_SECRET_KEY');
        $endpoint = env('BIZFLY_ENDPOINT');
        $region = env('BIZFLY_REGION');

        $credentials = new Credentials($accessKey, $secretKey);

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => $credentials,
            'endpoint' => $endpoint,
            'signature_version' => 'v4',
            'http' => [
                'verify' => false, // Tắt xác minh SSL
            ],
        ]);
    }

    public function upload($bucket, $key, $sourceFile)
    {
        return $this->s3Client->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $sourceFile,
            'ACL' => 'public-read',
        ]);
    }

    public function getUrl($bucket, $key)
    {
        return $this->s3Client->getObjectUrl($bucket, $key);
    }
}
