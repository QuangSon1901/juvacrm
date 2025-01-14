<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_consultation_logs';

    protected $fillable = [
        'consultation_id',
        'user_id',
        'message',
        'status',
        'created_at',
        'updated_at',
    ];
}
