<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bs05Semester extends Model
{
    use HasFactory;
    protected $table = 'bs05_semesters';
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function admissionSemester()
    {
        return $this->hasMany(Bs10Studentdb::class, 'admission_semester_id');
    }



}
