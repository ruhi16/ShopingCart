<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex25Settings extends Model
{
    use HasFactory;
    protected $table = 'ex25_settings';
    protected $guarded = ['id'];

    public function myclass()
    {
        return $this->belongsTo(Bs04Myclass::class, 'myclass_id');
    }

    public function section()
    {
        return $this->belongsTo(Bs06Section::class, 'section_id');
    }

    public function semester()
    {
        return $this->belongsTo(Bs05Semester::class, 'semester_id');
    }

    public function examDetail()
    {
        return $this->belongsTo(Ex24Detail::class, 'exam_detail_id');
    }

    public function subject()
    {
        return $this->belongsTo(Bs07Subject::class, 'subject_id');
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
