<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex26MarksGrade extends Model
{
    use HasFactory; 
    protected $table = 'ex26_marks_grades';
    protected $guarded = ['id'];
}
