<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex30MarksEntry extends Model
{
    use HasFactory;
    protected $table = 'ex30_marks_entries';
    protected $guarded = ['id'];
    
    // Ensure increments is enabled for auto-incrementing ID
    public $incrementing = true;
    protected $keyType = 'int';

    public function studentcr()
    {
        return $this->belongsTo(Bs11Studentcr::class, 'studentcr_id');
    }

    public function myclass()
    {
        return $this->belongsTo(Bs04Myclass::class, 'current_myclass_id');
    }

    public function section()
    {
        return $this->belongsTo(Bs06Section::class, 'current_section_id');
    }

    public function semester()
    {
        return $this->belongsTo(Bs05Semester::class, 'current_semester_id');
    }

    public function subject()
    {
        return $this->belongsTo(Bs07Subject::class, 'subject_id');
    }

    public function examDetail()
    {
        return $this->belongsTo(Ex24Detail::class, 'exam_detail_id');
    }

    public function examSetting()
    {
        return $this->belongsTo(Ex25Settings::class, 'exam_setting_id');
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
