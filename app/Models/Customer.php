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
        'last_interaction_date',
        'lead_score',
        'created_at',
        'updated_at',
    ];

    const TYPE_LEAD = 0;         // Khách hàng tiềm năng
    const TYPE_PROSPECT = 1;     // Khách hàng chưa sử dụng dịch vụ
    const TYPE_CUSTOMER = 2;     // Khách hàng đã sử dụng dịch vụ

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
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'customer_id');
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

    public function convertToProspect()
    {
        $this->type = self::TYPE_PROSPECT;
        $this->save();
        return $this;
    }

    public function convertToCustomer()
    {
        $this->type = self::TYPE_CUSTOMER;
        $this->service_usage_count = $this->service_usage_count + 1;
        $this->save();
        return $this;
    }

    public function getTypeName()
    {
        switch ($this->type) {
            case self::TYPE_LEAD:
                return 'Khách hàng tiềm năng';
            case self::TYPE_PROSPECT:
                return 'Khách hàng chưa sử dụng dịch vụ';
            case self::TYPE_CUSTOMER:
                return 'Khách hàng đã sử dụng dịch vụ';
            default:
                return 'Không xác định';
        }
    }

    // Cập nhật ngày tương tác gần nhất
    public function updateLastInteraction()
    {
        $this->last_interaction_date = now();
        $this->save();
        return $this;
    }

    // Tính điểm khách hàng tiềm năng
    public function calculateLeadScore()
    {
        $score = 0;
        
        // Có email +20 điểm
        if (!empty($this->email)) $score += 20;
        
        // Có SĐT +30 điểm
        if (!empty($this->phone)) $score += 30;
        
        // Có địa chỉ +10 điểm
        if (!empty($this->address)) $score += 10;
        
        // Có quan tâm dịch vụ +5 điểm cho mỗi dịch vụ
        if (!empty($this->services)) {
            $serviceCount = count(explode('|', $this->services));
            $score += $serviceCount * 5;
        }
        
        // Đã tương tác gần đây (trong 7 ngày) +15 điểm
        if (!empty($this->last_interaction_date) && now()->diffInDays($this->last_interaction_date) <= 7) {
            $score += 15;
        }
        
        $this->lead_score = $score;
        $this->save();
        
        return $score;
    }
}
