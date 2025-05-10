<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserDepartment;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Gửi thông báo đến người dùng
     */
    public static function send(
        $userId, 
        $title, 
        $content, 
        $type = 'general', 
        $actionUrl = null, 
        $icon = 'ki-notification', 
        $iconColor = 'primary', 
        $importance = 5, 
        $senderId = null
    ) {
        return Notification::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'action_url' => $actionUrl,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'importance' => $importance,
            'is_read' => false
        ]);
    }
    
    /**
     * Gửi thông báo đến nhiều người dùng
     */
    public static function sendMultiple(
        $userIds, 
        $title, 
        $content, 
        $type = 'general', 
        $actionUrl = null, 
        $icon = 'ki-notification', 
        $iconColor = 'primary', 
        $importance = 5, 
        $senderId = null
    ) {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = self::send(
                $userId, 
                $title, 
                $content, 
                $type, 
                $actionUrl, 
                $icon, 
                $iconColor, 
                $importance, 
                $senderId
            );
        }
        
        return $notifications;
    }
    
    /**
     * Gửi thông báo đến người dùng có quyền cụ thể
     */
    public static function sendToUsersWithPermission(
        $permission, 
        $title, 
        $content, 
        $type = 'general', 
        $actionUrl = null, 
        $icon = 'ki-notification', 
        $iconColor = 'primary', 
        $importance = 5, 
        $senderId = null,
        $excludeUserId = null
    ) {
        // Lấy danh sách người dùng có quyền cụ thể
        $userIds = self::getUsersWithPermission($permission, $excludeUserId);
        
        if (empty($userIds)) {
            return [];
        }
        
        return self::sendMultiple(
            $userIds, 
            $title, 
            $content, 
            $type, 
            $actionUrl, 
            $icon, 
            $iconColor, 
            $importance, 
            $senderId
        );
    }
    
    /**
     * Lấy danh sách ID người dùng có quyền cụ thể
     */
    public static function getUsersWithPermission($permission, $excludeUserId = null)
    {
        $query = DB::table('tbl_users as u')
            ->join('tbl_user_departments as ud', 'u.id', '=', 'ud.user_id')
            ->join('tbl_role_permissions as rp', function ($join) {
                $join->on('ud.department_id', '=', 'rp.department_id')
                     ->on('ud.level_id', '=', 'rp.level_id');
            })
            ->join('tbl_permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('p.slug', $permission)
            ->where('u.is_active', 1)
            ->where('ud.is_active', 1);
            
        if ($excludeUserId) {
            $query->where('u.id', '!=', $excludeUserId);
        }
        
        // Thêm super admin
        $userIds = $query->select('u.id')->distinct()->pluck('id')->toArray();
        
        $superAdmins = User::where('is_super_admin', 1)
            ->where('is_active', 1);
            
        if ($excludeUserId) {
            $superAdmins->where('id', '!=', $excludeUserId);
        }
        
        $superAdminIds = $superAdmins->pluck('id')->toArray();
        
        return array_unique(array_merge($userIds, $superAdminIds));
    }
}