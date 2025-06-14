<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcCategory extends Component
{

    public $categories = null;

    public function mount(){
        $this->categories = \App\Models\Category::all();

        


    }



    public function render()
    {
        return view('livewire.lc-category');
    }
}
