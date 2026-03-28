<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bs11Studentcr extends Model
{
    use HasFactory;
    protected $table = 'bs11_studentcrs';
    protected $guarded = ['id'];

    public function studentdb(){
        return $this->belongsTo(Bs10Studentdb::class, 'studentdb_id');
    }

    public function currentMyclass(){
        return $this->belongsTo(Bs04Myclass::class, 'current_myclass_id');
    }
    public function currentSection(){
        return $this->belongsTo(Bs06Section::class, 'current_section_id');
    }
    public function currentSemester(){
        return $this->belongsTo(Bs05Semester::class, 'current_semester_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    
}
