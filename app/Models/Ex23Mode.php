<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex23Mode extends Model
{
    use HasFactory;
    protected $table = 'ex23_modes';
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('is_active', 0);
    }
}
