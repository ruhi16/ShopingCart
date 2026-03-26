<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ex20Name;
use App\Models\Session;
use App\Models\School;

class Ex20NameComp extends Component
{
    public $names, $name, $ex20_name_id;
    public $schools;
    public $isOpen = 0;

    public function render()
    {
        $this->names = Ex20Name::all();
        $this->schools = School::active()->get();
        return view('livewire.ex20-name-comp')
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
        $this->ex20_name_id = 0;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        Ex20Name::updateOrCreate(['id' => $this->ex20_name_id ?? null], [
            'name' => $this->name,
            'is_active' => 1,
            'school_id' => School::find(auth()->user()->school_id ?? 1)->id, 
            //School::active()->first()->id ?? 1, // auth()->user()->school_id
            'session_id' => Session::active()->first()->id ?? 1, // auth()->user()->session_id
            // 
        ]);

        session()->flash('message',
            $this->ex20_name_id ? 'Name Updated Successfully.' : 'Name Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $name = Ex20Name::findOrFail($id);
        $this->ex20_name_id = $id;
        $this->name = $name->name;

        $this->openModal();
    }

    public function delete($id)
    {
        Ex20Name::find($id)->delete();
        session()->flash('message', 'Name Deleted Successfully.');
    }
}
