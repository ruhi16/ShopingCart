<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function purchase(){
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }


    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function purchaseUnit(){
        return $this->belongsTo(Unit::class, 'purchase_unit_id', 'id');
    }

    

}
