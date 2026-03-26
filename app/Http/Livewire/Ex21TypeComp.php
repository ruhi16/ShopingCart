<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ex21Type;
use App\Models\School;
use App\Models\Session;


class Ex21TypeComp extends Component
{
    public $types, $name, $ex21_type_id;
    public $isOpen = 0;

    public function render()
    {
        $this->types = Ex21Type::all();
        return view('livewire.ex21-type-comp')
            // ->layout('livewire.lc-main-layout')
            ;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields(){
        $this->name = '';
        $this->ex21_type_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        Ex21Type::updateOrCreate(['id' => $this->ex21_type_id], [
            'name' => $this->name,
            'is_active' => 1,
            'school_id' => auth()->user()->school_id, 
            'session_id' => Session::active()->first()->id ?? 1, // auth()->user()->session_id
            //
            
        ]);

        session()->flash('message',
            $this->ex21_type_id ? 'Type Updated Successfully.' : 'Type Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $type = Ex21Type::findOrFail($id);
        $this->ex21_type_id = $id;
        $this->name = $type->name;

        $this->openModal();
    }

    public function delete($id)
    {
        Ex21Type::find($id)->delete();
        session()->flash('message', 'Type Deleted Successfully.');
    }
}
