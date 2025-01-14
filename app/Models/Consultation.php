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
    ];

    public function consultation_logs()
    {
        return $this->hasMany(ConsultationLog::class)->orderBy('created_at', 'asc');;
    }
}
