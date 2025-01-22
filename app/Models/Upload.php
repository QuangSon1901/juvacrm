<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $table = 'tbl_uploads';

    protected $fillable = [
        'user_id',
        'driver_id',
        'name',
        'type',
        'size',
        'extension',
        'details',
        'fk_key',
        'fk_value',
        'action',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
