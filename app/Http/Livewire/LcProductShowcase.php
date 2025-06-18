<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcProductShowcase extends Component
{

    public $products = null;


    public function mount(){
        $this->products = \App\Models\Product::with('item')->get();

        // dd($this->products);
    }
    public function render()
    {
        return view('livewire.lc-product-showcase');
    }
}
