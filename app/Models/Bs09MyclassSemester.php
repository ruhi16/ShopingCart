<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bs09MyclassSemester extends Model
{
    use HasFactory;
    protected $table = 'bs09_myclass_semesters';
    protected $guarded = ['id'];

    public function myclass()
    {
        return $this->belongsTo(Bs04Myclass::class, 'myclass_id');
    }

    public function semester()
    {
        return $this->belongsTo(Bs05Semester::class, 'semester_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function settings()
    {
        return $this->hasMany(Ex25Settings::class, 'myclass_id', 'myclass_id')
            ->where('semester_id', $this->semester_id);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
