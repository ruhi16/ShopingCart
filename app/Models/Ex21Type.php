<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex21Type extends Model
{
    use HasFactory;
    protected $table = 'ex21_types';
    protected $guarded = ['id'];

    public function scopeActive($query){
        return $query->where('is_active', 1);
    }
    
    public function scopeInactive($query){
        return $query->where('is_active', 0);
    }
}
