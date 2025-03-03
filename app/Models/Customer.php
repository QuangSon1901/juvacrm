<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'tbl_customers';
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
        return $this->hasMany(Consultation::class)->where('is_deleted', 0)->orderBy('created_at', 'asc');
    }

    /**
     * Lấy các hợp đồng mà khách hàng là nhà cung cấp
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'provider_id');
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

    public function scopeFilterByServices($query, $services)
    {
        if (!empty($services))
            return $query->where('services', 'like', "%$services%");
        return $query;
    }

    public function scopeFilterByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status_id', $status);
        };
        return $query;
    }

    public function scopeFilterByClass($query, $class)
    {
        if ($class) {
            return $query->where('class_id', $class);
        };
        return $query;
    }

    public function scopeFilterByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFilterMyCustomer($query, $staff)
    {
        if ($staff) {
            return $query->where('user_id', Session::get(ACCOUNT_CURRENT_SESSION)['id']);
        };
        return $query;
    }

    public function scopeFilterBlackList($query, $is)
    {
        return $query->where('is_active', '!=', $is);
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) return $query->where('name', 'like', "%$search%")
                                        ->orWhere('phone', 'like', "%$search%")
                                        ->orWhere('email', 'like', "%$search%")
                                        ->orWhere('company', 'like', "%$search%");
        return $query;
    }
}
