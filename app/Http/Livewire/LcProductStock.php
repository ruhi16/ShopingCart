<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\PurchaseDetail;
use App\Models\Purchase;

class LcProductStock extends Component
{
    public $purchases = null;
    public $purchaseDetails = null;
    public function mount(){
        $this->purchases =  Purchase::all();
        $this->purchaseDetails = PurchaseDetail::all();

        // dd($this->purchase_details);
    }


    public function render()
    {
        return view('livewire.lc-product-stock');
    }
}
