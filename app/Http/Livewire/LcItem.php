<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Item;

class LcItem extends Component{

    public $items = null;

    public function mount(){
        $this->items = Item::all();
    }

    public function render()
    {
        return view('livewire.lc-item');
    }
}
