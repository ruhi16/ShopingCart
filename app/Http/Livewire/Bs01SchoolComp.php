<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\School;

class Bs01SchoolComp extends Component
{
    public $schools, $name, $address, $school_id;
    public $isOpen = 0;

    public function render()
    {
        $this->schools = School::all();
        return view('livewire.bs01-school-comp')->layout('livewire.lc-main-layout');
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
        $this->address = '';
        $this->school_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'address' => 'required',
        ]);

        School::updateOrCreate(['id' => $this->school_id], [
            'name' => $this->name,
            'address' => $this->address
        ]);

        session()->flash('message',
            $this->school_id ? 'School Updated Successfully.' : 'School Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $school = School::findOrFail($id);
        $this->school_id = $id;
        $this->name = $school->name;
        $this->address = $school->address;

        $this->openModal();
    }

    public function delete($id)
    {
        School::find($id)->delete();
        session()->flash('message', 'School Deleted Successfully.');
    }
}
