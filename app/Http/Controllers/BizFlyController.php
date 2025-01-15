<?php

namespace App\Http\Controllers;

use App\Services\BizFlyStorageService;
use Illuminate\Http\Request;

class BizFlyController extends Controller
{
    protected $bizFlyService;

    public function __construct(BizFlyStorageService $bizFlyService)
    {
        $this->bizFlyService = $bizFlyService;
    }

    public function uploadFile(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $bucket = env('BIZFLY_BUCKET');
        $key = 'uploads/' . $file->getClientOriginalName();

        $result = $this->bizFlyService->upload($bucket, $key, $file->getPathname());

        return response()->json(['url' => $result['ObjectURL']], 200);
    }

    public function getFileUrl($key)
    {
        $bucket = env('BIZFLY_BUCKET');
        $key = 'uploads/' . $key;
        $url = $this->bizFlyService->getUrl($bucket, $key);

        return $url;
    }
}
