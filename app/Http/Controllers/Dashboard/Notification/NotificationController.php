<?php

namespace App\Http\Controllers\Dashboard\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo chưa đọc
     */
    public function getUnreadNotifications()
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        $notifications = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
        
        $html = view('dashboard.notification.notification-list', ['notifications' => $notifications])->render();
        
        return response()->json([
            'status' => 200,
            'html' => $html,
            'unreadCount' => $unreadCount
        ]);
    }
    
    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead(Request $request)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        if ($request->has('id')) {
            // Đánh dấu một thông báo cụ thể
            $notification = Notification::where('id', $request->id)
                ->where('user_id', $userId)
                ->first();
                
            if (!$notification) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông báo'
                ]);
            }
            
            $notification->markAsRead();
            
            LogService::saveLog([
                'action' => 'MARK_NOTIFICATION_AS_READ',
                'ip' => $request->getClientIp(),
                'details' => "Đánh dấu đã đọc thông báo #" . $notification->id,
                'fk_key' => 'tbl_notifications|id',
                'fk_value' => $notification->id,
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Đã đánh dấu đã đọc'
            ]);
        } else {
            // Đánh dấu tất cả thông báo
            $count = Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
                
            LogService::saveLog([
                'action' => 'MARK_ALL_NOTIFICATIONS_AS_READ',
                'ip' => $request->getClientIp(),
                'details' => "Đánh dấu đã đọc tất cả thông báo",
                'fk_key' => null,
                'fk_value' => null,
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Đã đánh dấu tất cả thông báo là đã đọc',
                'count' => $count
            ]);
        }
    }
    
    /**
     * Hiển thị trang quản lý thông báo
     */
    public function index()
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('dashboard.notification.index', compact('notifications'));
    }
    
    /**
     * Lấy dữ liệu thông báo cho trang quản lý
     */
    public function getData(Request $request)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        $query = Notification::where('user_id', $userId);
        
        // Lọc theo loại nếu có
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Lọc theo trạng thái đã đọc/chưa đọc
        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read == 'true');
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $html = view('dashboard.notification.notification-table', ['notifications' => $notifications])->render();
        
        return response()->json([
            'status' => 200,
            'html' => $html,
            'pagination' => $notifications->links()->toHtml()
        ]);
    }
    
    /**
     * Xóa thông báo
     */
    public function delete(Request $request)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        if ($request->has('id')) {
            // Xóa một thông báo cụ thể
            $notification = Notification::where('id', $request->id)
                ->where('user_id', $userId)
                ->first();
                
            if (!$notification) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông báo'
                ]);
            }
            
            $notificationId = $notification->id;
            $notification->delete();
            
            LogService::saveLog([
                'action' => 'DELETE_NOTIFICATION',
                'ip' => $request->getClientIp(),
                'details' => "Xóa thông báo #" . $notificationId,
                'fk_key' => 'tbl_notifications|id',
                'fk_value' => $notificationId,
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa thông báo'
            ]);
        } else {
            // Xóa tất cả thông báo đã đọc
            $count = Notification::where('user_id', $userId)
                ->where('is_read', true)
                ->delete();
                
            LogService::saveLog([
                'action' => 'DELETE_ALL_READ_NOTIFICATIONS',
                'ip' => $request->getClientIp(),
                'details' => "Xóa tất cả thông báo đã đọc",
                'fk_key' => null,
                'fk_value' => null,
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa tất cả thông báo đã đọc',
                'count' => $count
            ]);
        }
    }
}