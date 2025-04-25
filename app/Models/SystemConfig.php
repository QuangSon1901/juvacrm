<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table = 'tbl_system_config';
    
    protected $fillable = [
        'config_key',
        'config_value',
        'description',
        'is_active',
    ];
    
    /**
     * Lấy giá trị của một cấu hình
     * 
     * @param string $key Khóa cấu hình
     * @param mixed $default Giá trị mặc định nếu không tìm thấy
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $config = self::where('config_key', $key)
                    ->where('is_active', 1)
                    ->first();
        return $config ? $config->config_value : $default;
    }
}