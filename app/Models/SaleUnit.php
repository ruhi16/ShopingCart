<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleUnit extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function saleDetail(){
        return $this->belongsTo(Unit::class, 'sale_unit_id', 'id');
    }

    
}
