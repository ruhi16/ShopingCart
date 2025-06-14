<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;


class LcProduct extends Component
{
    public $products = null;

    public function mount(){
        $this->products = Product::all();


    }



    public function render()
    {
        return view('livewire.lc-product');
    }
}
