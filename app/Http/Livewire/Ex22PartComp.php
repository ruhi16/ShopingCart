<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ex22Part;
use App\Models\Session;
use App\Models\School;


class Ex22PartComp extends Component
{
    public $parts, $name, $ex22_part_id;
    public $isOpen = 0;

    public function render()
    {
        $this->parts = Ex22Part::all();
        return view('livewire.ex22-part-comp')
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
        $this->ex22_part_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        Ex22Part::updateOrCreate(['id' => $this->ex22_part_id], [
            'name' => $this->name,
            'is_active' => 1,
            'school_id' => auth()->user()->school_id,
            'session_id' => Session::active()->first()->id ?? 1,
        ]);

        session()->flash('message',
            $this->ex22_part_id ? 'Part Updated Successfully.' : 'Part Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $part = Ex22Part::findOrFail($id);
        $this->ex22_part_id = $id;
        $this->name = $part->name;

        $this->openModal();
    }

    public function delete($id)
    {
        Ex22Part::find($id)->delete();
        session()->flash('message', 'Part Deleted Successfully.');
    }
}
