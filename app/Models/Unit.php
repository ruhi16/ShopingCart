<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function purchaseUnits(){
        return $this->hasMany(PurchaseDetail::class, 'purchase_unit_id', 'id');
    }


    public function saleUnits(){
        return $this->hasMany(SaleUnit::class, 'sale_unit_id', 'id');
    }
    


}
