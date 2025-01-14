<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Tên bảng trong database
    protected $table = 'tbl_customers';

    // Các thuộc tính có thể gán (fillable)
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'company',
        'user_id',
        'type',
        'source_id',
        'service_usage_count',
        'services',
        'class_id',
        'contact_methods',
        'status_id',
        'note',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function source()
    {
        return $this->belongsTo(CustomerLead::class, 'source_id', 'id');
    }

    public function classification()
    {
        return $this->belongsTo(CustomerClass::class, 'class_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(CustomerLead::class, 'status_id', 'id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class)->orderBy('created_at', 'asc');
    }

    public function getServicesArray()
    {
        if (empty($this->services)) {
            return [];
        }

        return Service::whereIn('id', explode('|', $this->services))->get()->toArray();
    }

    public function getContactsArray()
    {
        if (empty($this->contact_methods)) {
            return [];
        }

        return CustomerLead::whereIn('id', explode('|', $this->contact_methods))->where('type', 0)->get()->toArray();
    }
}
