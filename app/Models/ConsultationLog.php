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
        'action',
        'created_at',
        'updated_at',
    ];

    public function getAttachmentsArray() {
        return Upload::select('id', 'name', 'type', 'size', 'extension', 'driver_id')->where('fk_key', 'tbl_consultation_logs|id')->where('fk_value', $this->id)->get()->toArray();
    }

    public function getServicesArray()
    {
        if (empty($this->services)) {
            return [];
        }

        return Service::whereIn('id', explode('|', $this->services))->get()->toArray();
    }
}
