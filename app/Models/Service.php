<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'tbl_services';
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'is_active'
    ];

    /**
     * Lấy danh mục dịch vụ
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Lấy các dịch vụ hợp đồng sử dụng dịch vụ này
     */
    public function contractServices()
    {
        return $this->hasMany(ContractService::class);
    }
}
