<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'description',
        'is_active',
        'remarks',
    ];


    public function scopeActive(Builder $query){
        return $query->where('is_active', 1);
    }

    public function scopeInactive($query){
        return $query->where('is_active', 0);
    }

}
