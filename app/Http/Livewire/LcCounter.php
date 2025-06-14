<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcCounter extends Component
{
    public $count;

    public function mount(){
        $this->count = 0;
    }

    public function inc(){
        $this->count++;
    }

    public function dec(){
        $this->count--;
    } 
    


    public function render()
    {
        return view('livewire.lc-counter');
    }
}
