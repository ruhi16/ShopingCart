<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcProductShowcase extends Component
{
    public $user = null;

    public $products = null;
    public $sales = null;

    public $curtProducts = null;

    public function mount(){
        $this->user = auth()->user();
        $this->products = \App\Models\Product::with('item')->get();
        $this->sales = \App\Models\Sale::get();

        $userSale = \App\Models\Sale::where('customer_id', $this->user->id)
            ->where('is_paid', false)
            ->first();

        $this->curtProducts = \App\Models\SaleDetail::
            where('sale_id', $userSale->id)
            ->get();

        


        // dd($this->products);
    }


    public function addToCart($productId){
        try{
            $sale = \App\Models\Sale::updateOrCreate([
                'customer_id' => $this->user->id,
                'is_paid' => false,
            ],[
                'product_id' => $productId,
                'quantity' => 1,
                'status' => 'pending'
            ]);

            // dd($sale);
            $saleDetails = \App\Models\SaleDetail::updateOrCreate([
                'sale_id' => $sale->id,
                'product_id' => $productId,
            ],[
                'sale_unit_qty' => 1,
                'sale_unit_rate' => 501,
            ]);



        } catch(Exception $e){
            session()->flush('error', $e->message);
        }

    }


    public function render()
    {
        return view('livewire.lc-product-showcase');
    }
}
