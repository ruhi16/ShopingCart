<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function user(){
        // return $this->belongsTo('User', 'user_id', 'id');
    }

    public function saleDetails(){
        return $this->hasMany(SaleDetail::class, 'sale_id', 'id');
    }




}
