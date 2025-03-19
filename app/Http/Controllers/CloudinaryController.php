<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Services\CloudinaryService;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CloudinaryController extends Controller
{
    private $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function uploadFile(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,svg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $file = $request->file('file');
            $uploadedFile = $this->cloudinaryService->uploadFile($file);

            Upload::create([
                'user_id'    => Session::get(ACCOUNT_CURRENT_SESSION)['id'] ?? 0,
                'type'       => $file->getClientMimeType(),
                'details'    => json_encode([
                    'original_name' => $file->getClientOriginalName(),
                    'extension'     => $file->getClientOriginalExtension(),
                ]),
                'name'       => $file->getClientOriginalName(),
                'size'       => $file->getSize(),
                'extension'  => $file->getClientOriginalExtension(),
                'driver_id'  => explode('/uploads/', $uploadedFile['url'])[1],
                'action'  => $request['action'] ?? MEDIA_DRIVER_UPLOAD,
                'fk_key' => $reuqest['fk_key'] ?? null,
                'fk_value' => $reuqest['fk_value'] ?? null,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Tải lên thành công.',
                'data' => [
                    'name' => $file->getClientOriginalName(),
                    'url' => $uploadedFile['url'],
                    'driver_id' => explode('/uploads/', $uploadedFile['url'])[1],
                    'secure_url' => $uploadedFile['secure_url'],
                    'extension' => $file->getClientOriginalExtension(),
                    'type' => $file->getClientMimeType(),
                    'size' => formatBytes($file->getSize()),
                ]
            ]);
        });
    }
}
