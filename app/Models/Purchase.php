<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function purchaseDetails(){
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id');
    }

   

}
