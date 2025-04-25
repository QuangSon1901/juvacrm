<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractService extends Model
{
    protected $table = 'tbl_contract_services';
    protected $fillable = [
        'contract_id', 'service_id', 'product_id', 'name', 'type', 
        'quantity', 'price', 'note', 'is_active', 'parent_id',
        'sample_image_id', 'result_image_id','service_type'
    ];

    /**
     * Lấy hợp đồng chứa dịch vụ này
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Lấy dịch vụ gốc
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Lấy sản phẩm liên quan
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Lấy dịch vụ cha
     */
    public function parent()
    {
        return $this->belongsTo(ContractService::class, 'parent_id');
    }

    /**
     * Lấy các dịch vụ con
     */
    public function subServices()
    {
        return $this->hasMany(ContractService::class, 'parent_id');
    }
}
