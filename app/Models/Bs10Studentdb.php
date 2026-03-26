<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bs10Studentdb extends Model
{
    use HasFactory;

    protected $table = 'bs10_studentdbs';
    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function admissionMyclass()
    {
        return $this->belongsTo(Bs04Myclass::class, 'admission_myclass_id');
    }

    public function admissionSemester()
    {
        return $this->belongsTo(Bs05Semester::class, 'admission_semester_id');
    }

    public function admissionSection()
    {
        return $this->belongsTo(Bs06Section::class, 'admission_section_id');
    }

    public function admissionSession()
    {
        return $this->belongsTo(Session::class, 'admission_session_id');
    }

    public function studentSubjects()
    {
        return $this->hasMany(Bs12StudentdbSubject::class, 'studentdb_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeInSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeInSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
