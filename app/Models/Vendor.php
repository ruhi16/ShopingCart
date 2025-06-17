<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $table = 'vendors';

    public function purchases(){
        return $this->hasMany(Purchase::class, 'vendor_id', 'id');
    }


}
