<?php
namespace App\Services;

use App\Models\ActivityLogs;
use Illuminate\Support\Facades\Session;

class LogService
{
    public static function saveLog($data)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        ActivityLogs::create([
            'user_id' => $userId,
            'action' => $data['action'] ?? '',
            'ip' => $data['ip'] ?? '',
            'details' => $data['details'] ?? '',
            'fk_key' => $data['fk_key'] ?? '',
            'fk_value' => $data['fk_value'] ?? '',
        ]);
    }
}
