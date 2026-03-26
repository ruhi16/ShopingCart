<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ex23Mode;
use App\Models\Session;
use App\Models\School;


class Ex23ModeComp extends Component
{
    public $modes, $name, $ex23_mode_id;
    public $isOpen = 0;

    public function render()
    {
        $this->modes = Ex23Mode::all();
        return view('livewire.ex23-mode-comp')
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
        $this->ex23_mode_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        Ex23Mode::updateOrCreate(['id' => $this->ex23_mode_id], [
            'name' => $this->name,
            'is_active' => 1,
            'school_id' => auth()->user()->school_id,
            'session_id' => Session::active()->first()->id ?? 1,
        ]);

        session()->flash('message',
            $this->ex23_mode_id ? 'Mode Updated Successfully.' : 'Mode Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $mode = Ex23Mode::findOrFail($id);
        $this->ex23_mode_id = $id;
        $this->name = $mode->name;

        $this->openModal();
    }

    public function delete($id)
    {
        Ex23Mode::find($id)->delete();
        session()->flash('message', 'Mode Deleted Successfully.');
    }
}
