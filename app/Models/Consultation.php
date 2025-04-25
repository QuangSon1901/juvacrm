<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table = 'tbl_consultations';

    protected $fillable = [
        'customer_id',
        'name',
        'created_at',
        'updated_at',
        'is_deleted',
    ];

    // Quan hệ với Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    // Quan hệ với ConsultationLog
    public function logs()
    {
        return $this->hasMany(ConsultationLog::class, 'consultation_id')
            ->orderBy('created_at', 'desc');
    }
    
    // Lấy trạng thái mới nhất của cuộc tư vấn
    public function getLatestStatus()
    {
        return $this->logs()->first();
    }
}
