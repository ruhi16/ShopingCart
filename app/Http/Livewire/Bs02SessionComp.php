<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Session;

class Bs02SessionComp extends Component
{
    public $sessions, $name, $start_date, $end_date, $session_id;
    public $isOpen = 0;

    public function render()
    {
        $this->sessions = Session::all();
        return view('livewire.bs02-session-comp')
            ->layout('livewire.lc-main-layout');
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
        $this->start_date = '';
        $this->end_date = '';
        $this->session_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        Session::updateOrCreate(['id' => $this->session_id], [
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('message',
            $this->session_id ? 'Session Updated Successfully.' : 'Session Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $session = Session::findOrFail($id);
        $this->session_id = $id;
        $this->name = $session->name;
        $this->start_date = $session->start_date->format('Y-m-d');
        $this->end_date = $session->end_date->format('Y-m-d');

        $this->openModal();
    }

    public function delete($id)
    {
        Session::find($id)->delete();
        session()->flash('message', 'Session Deleted Successfully.');
    }
}
