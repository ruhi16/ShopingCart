<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeInactive($query)
    {
        return $query->where('is_active', 0);
    }

    public function scopeActive($query){
        return $query->where('is_active', 1);
    }
    // when want only active session
    // use Session::active()
}
