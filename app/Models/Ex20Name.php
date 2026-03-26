<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex20Name extends Model
{
    use HasFactory;
    protected $table = 'ex20_names';
    protected $guarded = ['id'];
    protected $fillable = ['name', 'is_active', 'school_id', 'session_id'];

    // public function scopeSearch($query, $search){
    //     return $query->where('name', 'like', '%' . $search . '%');
    // }

    // public function scopeFilter($query, $filters){
    //     return $query->when($filters['search'] ?? null, function ($query, $search) {
    //         return $query->search($search);
    //     });
    // }

    // public function scopeSort($query, $sort){
    //     return $query->when($sort, function ($query, $sort) {
    //         return $query->orderBy('name', $sort);
    //     });
    // }

    // public function scopePaginate($query, $paginate){
    //     return $query->when($paginate, function ($query, $paginate) {
    //         return $query->paginate($paginate);
    //     });
    // }

    // public function scopeGetAllNames($query){
    //     return $query->select('name')->distinct()->get();
    // }

    public function scopeActive($query){
        return $query->where('is_active', 1);
    }

    public function scopeInactive($query){
        return $query->where('is_active', 0);
    }


}
