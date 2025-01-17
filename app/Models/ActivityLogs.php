<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    use HasFactory;
    protected $table = 'tbl_activity_logs';
    protected $fillable = [
        'user_id',
        'action',
        'ip',
        'details',
        'fk_key',
        'fk_value',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
