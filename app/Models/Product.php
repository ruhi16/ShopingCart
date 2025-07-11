<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function item(){
        return $this->belongsTo(Item::class, 'item_id', 'id');

    }

    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');

    }

    public function PurchaseDetails(){
        return $this->hasMany(PurchaseDetail::class, 'product_id', 'id');
    }

    public function saleDetails(){
        return $this->hasMany(SaleDetail::class, 'product_id', 'id');
    }


}
