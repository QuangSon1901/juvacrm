<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskConfig extends Model
{
    use HasFactory;
    protected $table = 'tbl_task_config';
    protected $fillable = [
        'name',
        'description',
        'type',
        'sort',
        'color',
        'is_active',
    ];
}
