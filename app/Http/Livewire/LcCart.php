<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcCart extends Component
{
    public $user = null;
    public $userSale = null;

    
    public $products = null;

    public function mount(){
        $this->user = auth()->user();
        $this->userSale = \App\Models\Sale::where('customer_id', $this->user->id)
            ->where('is_paid', false)
            // ->with('saleDetails')
            ->first();


    }
    public function render()
    {
        return view('livewire.lc-cart');
    }
}
