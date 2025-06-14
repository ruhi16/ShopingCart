<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcUnit extends Component
{
    public $units = null;

    public function mount(){
        $this->units = \App\Models\Unit::all();
        
    }




    public function render()
    {
        return view('livewire.lc-unit');
    }
}
