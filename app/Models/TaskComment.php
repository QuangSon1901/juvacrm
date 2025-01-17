<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;
    protected $table = 'tbl_task_comments';
    protected $fillable = [
        'user_id',
        'task_id',
        'message',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
