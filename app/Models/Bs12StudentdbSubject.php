<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bs12StudentdbSubject extends Model
{
    use HasFactory;

    protected $table = 'bs12_studentdb_subjects';
    protected $guarded = ['id'];

    public function studentdb()
    {
        return $this->belongsTo(Bs10Studentdb::class, 'studentdb_id');
    }

    public function subject()
    {
        return $this->belongsTo(Bs07Subject::class, 'subject_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
