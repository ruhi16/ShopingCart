<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex31MarksResult extends Model
{
    use HasFactory;
    protected $table = 'ex31_marks_results';
    protected $guarded = ['id'];
}
