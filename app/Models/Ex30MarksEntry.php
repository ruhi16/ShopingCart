<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ex30MarksEntry extends Model
{
    use HasFactory;
    protected $table = 'ex30_marks_entries';
    protected $guarded = ['id'];
    
}
