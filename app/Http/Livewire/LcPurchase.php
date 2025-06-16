<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcPurchase extends Component
{
    public $purchases = null;
    public $showModal = false;

    public $showProductModal = false;



    public function mount(){
        $this->purchases = \App\Models\Purchase::with('purchaseDetails')->get();
    }


    public function openModal(){
        $this->showModal = true;
    }

    public function closeModal(){
        $this->showModal = false;
    }

    public function openProductModal(){
        $this->showProductModal = true;
    }
    public function closeProductModal(){
        $this->showProductModal = false;
    }



    public function render()
    {
        return view('livewire.lc-purchase');
    }
}
