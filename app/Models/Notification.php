<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tbl_notifications';
    
    protected $fillable = [
        'user_id', 
        'sender_id', 
        'title', 
        'content', 
        'type', 
        'action_url', 
        'icon', 
        'icon_color', 
        'importance', 
        'is_read'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    // Lấy thông tin người gửi
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    // Lấy thông tin người nhận
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Scope lấy thông báo chưa đọc
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    // Scope lấy thông báo theo loại
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    // Đánh dấu thông báo đã đọc
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
        
        return $this;
    }
    
    // Format thời gian
    public function getTimeAgo()
    {
        return \Carbon\Carbon::parse($this->created_at)->diffForHumans();
    }
}