<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bs07Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject_code',
        'subject_type',
        'category_id',
        'session_id',
        'school_id',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
