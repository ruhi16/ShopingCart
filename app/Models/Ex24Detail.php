<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex24Detail extends Model
{
    use HasFactory;
    protected $table = 'ex24_details';
    protected $guarded = ['id'];

    public function examName()
    {
        return $this->belongsTo(Ex20Name::class, 'exam_name_id');
    }

    public function examType()
    {
        return $this->belongsTo(Ex21Type::class, 'exam_type_id');
    }

    public function examPart()
    {
        return $this->belongsTo(Ex22Part::class, 'exam_part_id');
    }

    public function examMode()
    {
        return $this->belongsTo(Ex23Mode::class, 'exam_mode_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function myclass()
    {
        return $this->belongsTo(Bs04Myclass::class, 'myclass_id');
    }

    public function semester()
    {
        return $this->belongsTo(Bs05Semester::class, 'semester_id');
    }
}
