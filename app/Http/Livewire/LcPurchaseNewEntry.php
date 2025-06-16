<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Vendor;
use App\Models\Category;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\PurchaseDetail;


class LcPurchaseNewEntry extends Component
{

    public $showProductModal = false;    

    public $vendors = null, $selectedVendor = null;
    public $invoiceNo = null, $invoiceDate = null;

    public $categories = null, $selectedCategory = null;
    public $items = null, $selectedItem = null;
    public $purchaseUnits = null, $selectedPurchaseUnit = null;

    public $purchaseRate = null, $purchaseQty = null, $purchaseAmount = null;

    public $products = null;

    public function mount(){
        $this->vendors = \App\Models\Vendor::all();
        $this->categories = \App\Models\Category::all();
        $this->items = \App\Models\Item::all();
        $this->purchaseUnits = \App\Models\Unit::all();

    }


    public function openProductModal(){
        $this->showProductModal = true;
    }
    public function closeProductModal(){
        $this->showProductModal = false;
    }

    public function updatedPurchaseRate(){
        $this->calculatePurchaseAmount();
        
    }
    public function updatedPurchaseQty(){
        $this->calculatePurchaseAmount();
        
    }

    public function calculatePurchaseAmount(){
        if($this->purchaseRate != null && $this->purchaseRate != null){
            $this->purchaseAmount = $this->purchaseRate * $this->purchaseQty;
        }else{
            $this->purchaseAmount = null;
        }
    }


    public function saveProductData(){

        try{
        $purchase = Purchase::updateOrCreate([
            'vendor_id' => $this->selectedVendor,
            'invoice_no' => $this->invoiceNo,        
            'invoice_date' => $this->invoiceDate,       

        ],[
            'order_id' => 100,
        ]);


        $product = Product::updateOrCreate([
            'category_id' => $this->selectedCategory,
            'item_id' => $this->selectedItem,
        ],[
            // 'purchase_id' => $purchase->id,
        ]);

        $purchaseDetail = PurchaseDetail::updateOrCreate([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
        ],[
            'purchase_unit_id' => $this->selectedPurchaseUnit,
            'purchase_unit_rate' => $this->purchaseRate,
            'purchase_unit_qty' => $this->purchaseQty,
            'purchase_amount' => $this->purchaseAmount,
            'purchase_adjustment' => 0,
            'purchase_amount_payable' => $this->purchaseAmount - 0,
            
        ]);



            session()->flash('success', 'Product saved successfully');
        } catch(Exception $e){
            
            session()->flash('error', $e->getMessage());
        }


        $this->refresh();
        $this->closeProductModal();

    }

    public function refresh(){
        

        // $this->products = null;
        // $this->selectedProduct = null;

        
        // $this->vendors = null;
        // $this->selectedVendor = null;
        
        $this->categories = null;
        $this->selectedCategory = null;
        
        $this->items = null;
        $this->selectedItem = null;
        
        $this->purchaseUnits = null;
        $this->selectedPurchaseUnit = null;
        
        $this->purchaseRate = null;
        $this->purchaseQty = null;
        $this->purchaseAmount = null;

        // $this->invoiceNo = null;
        // $this->invoiceDate = null;



    }




    public function render()
    {
        return view('livewire.lc-purchase-new-entry');
    }
}
