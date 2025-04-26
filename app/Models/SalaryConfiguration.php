<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryConfiguration extends Model
{
    protected $table = 'tbl_salary_configurations';
    
    protected $fillable = [
        'user_id',
        'type',
        'hourly_rate',
        'monthly_salary',
        'overtime_rate',
        'attendance_bonus_rate',
        'tax_rate',
        'insurance_rate',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public static function getConfiguration($userId = null, $type = 'fulltime')
    {
        $config = self::where('type', $type);
        
        if ($userId) {
            $config = $config->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhereNull('user_id');
            })->orderBy('user_id', 'desc'); // Ưu tiên cấu hình riêng
        } else {
            $config = $config->whereNull('user_id');
        }
        
        return $config->first();
    }
}