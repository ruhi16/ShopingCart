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



class LcPurchaseNewEntryV2 extends Component
{
    public $vendors = null, $selectedVendor = null;
    public $invoiceNo = null, $invoiceDate = null;

    public $categories = null, $selectedCategory = null;
    public $items = null, $selectedItem = null;
    public $purchaseUnits = null, $selectedPurchaseUnit = null;


    public $rowCounter = 0;
    public $productDetails = [];


    public function mount(){
        $this->vendors = \App\Models\Vendor::all();
        $this->categories = \App\Models\Category::all();
        $this->items = \App\Models\Item::all();
        $this->purchaseUnits = \App\Models\Unit::all();

        $this->addProductRow();
    }

    public function addProductRow(){
        $this->productDetails[$this->rowCounter] = [
            'category_id' => '',
            'item_id' => '',
            'purchase_unit_id' => '',
            'quantity' => 0,
            'rate' => 0,
            'amount' => 0,
        ];
        $this->rowCounter++;


        $this->refresh();
    }

    public function updateProductOptions($index)
    {
        $categoryId = $this->productDetails[$index]['category_id'];
        // dd($categoryId, $index);

        if ($categoryId) {
            // Reset product selection when category changes
            $this->productDetails[$index]['item_id'] = '';
            $this->productDetails[$index]['amount'] = 0;
            
            // This will trigger the view to update product options
            // $this->emit('categoryChanged', $index, $categoryId);
        }
    }

    public function calculateAmount($index){
        $this->productDetails[$index]['amount'] = $this->productDetails[$index]['quantity'] * $this->productDetails[$index]['rate'];
    }



    public function getTotalAmount(){
        // $totalAmount = 0;
        return collect($this->productDetails)->sum('amount');

    }

    public function saveProductDetails(){

        // $this->validate([
        //     // 'vendor_id' => 'required',
        //     // 'invoice_no' => 'required',
        //     // 'invoice_date' => 'required',
        // ]);

        $this->validate([
            'productDetails.*.category_id' => 'required|exists:categories,id',
            'productDetails.*.item_id' => 'required|exists:items,id',
            'productDetails.*.purchase_unit_id' => 'required|exists:units,id',
            'productDetails.*.quantity' => 'required|numeric|min:0',
            'productDetails.*.rate' => 'required|numeric|min:0',
        ]);

        try{
            // DB::beginTransaction();


            $product = Product::updateOrCreate([
                'category_id' => $this->selectedCategory,
                'item_id' => $this->selectedItem,
                    ],[
                        // 'purchase_id' => $purchase->id,
                ]);

            $purchase = Purchase::updateOrCreate([
                'vendor_id' => $this->selectedVendor,
                'invoice_no' => $this->invoiceNo,        
                'invoice_date' => $this->invoiceDate,       

            ],[
                'order_id' => 100,
            ]);

            foreach($this->productDetails as $detail){
                $purchaseDetails = PurchaseDetail::updateOrCreate([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                ],[
                    'purchase_unit_id' => $detail['purchase_unit_id'],
                    'purchase_unit_rate' => $detail['rate'],
                    'purchase_unit_qty' => $detail['quantity'],
                    'purchase_amount' => $detail['amount'],
                    'purchase_adjustment' => 0,
                    'purchase_amount_payable' => $detail['amount'] - 0,
                ]);
            }

            // Db::commitTransaction();

            session()->flash('success', 'Product details saved successfully!');
            // $this->refresh();




        }catch(Exception $e){
            session()->flash('error', $e->getMessage());
        }




        

        session()->flash('message', 'Product details saved successfully!');
    }




    public function removeProductRow($index){
        array_splice($this->productDetails, $index, 1);
        $this->rowCounter--;
    }




    public function render()
    {
        return view('livewire.lc-purchase-new-entry-v2');
    }

    public function refresh(){

    }
}
