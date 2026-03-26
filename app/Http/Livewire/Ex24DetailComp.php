<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ex24Detail;
use App\Models\Ex20Name;
use App\Models\Ex21Type;
use App\Models\Ex22Part;
use App\Models\Ex23Mode;
use App\Models\Session;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class Ex24DetailComp extends Component
{
    public $details, $detail_id;
    public $exam_name_id, $exam_type_id, $exam_part_id, $exam_mode_id;
    public $is_active = 1;
    public $remarks;
    public $isOpen = 0;
    public $names, $types, $parts, $modes, $sessions, $schools;
    public $search = '';
    public $selected_session_id, $selected_school_id;

    public function render()
    {
        $this->loadDropdownData();
        
        $query = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode', 'session', 'school'])
            ->where('school_id', auth()->user()->school_id);
        
        if ($this->search) {
            $query->whereHas('examName', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }
        
        $this->details = $query->orderBy('id', 'desc')->get();
        
        return view('livewire.ex24-detail-comp')
            // ->layout('livewire.lc-main-layout')
            ;
    }

    private function loadDropdownData()
    {
        $this->names = Ex20Name::active()->get();
        $this->types = Ex21Type::active()->get();
        $this->parts = Ex22Part::all();
        $this->modes = Ex23Mode::all();
        $this->sessions = Session::active()->get();
        $this->schools = School::active()->get();
        
        // Set defaults
        if (!$this->selected_session_id) {
            $activeSession = Session::active()->first();
            $this->selected_session_id = $activeSession ? $activeSession->id : 1;
        }
        if (!$this->selected_school_id) {
            $this->selected_school_id = auth()->user()->school_id ?? 1;
        }
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

    private function resetInputFields()
    {
        $this->exam_name_id = '';
        $this->exam_type_id = '';
        $this->exam_part_id = '';
        $this->exam_mode_id = '';
        $this->is_active = 1;
        $this->remarks = '';
        $this->detail_id = '';
    }

    public function store()
    {
        $this->validate([
            'exam_name_id' => 'required|exists:ex20_names,id',
            'exam_type_id' => 'required|exists:ex21_types,id',
            'exam_part_id' => 'required|exists:ex22_parts,id',
            'exam_mode_id' => 'required|exists:ex23_modes,id',
        ]);

        Ex24Detail::updateOrCreate(['id' => $this->detail_id], [
            'exam_name_id' => $this->exam_name_id,
            'exam_type_id' => $this->exam_type_id,
            'exam_part_id' => $this->exam_part_id,
            'exam_mode_id' => $this->exam_mode_id,
            'session_id' => $this->selected_session_id,
            'school_id' => $this->selected_school_id,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]);

        session()->flash('message',
            $this->detail_id ? 'Detail Updated Successfully.' : 'Detail Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $detail = Ex24Detail::findOrFail($id);
        $this->detail_id = $id;
        $this->exam_name_id = $detail->exam_name_id;
        $this->exam_type_id = $detail->exam_type_id;
        $this->exam_part_id = $detail->exam_part_id;
        $this->exam_mode_id = $detail->exam_mode_id;
        $this->selected_session_id = $detail->session_id;
        $this->selected_school_id = $detail->school_id;
        $this->is_active = $detail->is_active;
        $this->remarks = $detail->remarks;

        $this->openModal();
    }

    public function delete($id)
    {
        Ex24Detail::find($id)->delete();
        session()->flash('message', 'Detail Deleted Successfully.');
    }

    /**
     * Generate all possible combinations of exam names, types, parts, and modes
     */
    public function generateCombinations()
    {
        $this->validate([
            'selected_session_id' => 'required|exists:sessions,id',
            'selected_school_id' => 'required|exists:schools,id',
        ]);

        $names = Ex20Name::active()->get();
        $types = Ex21Type::active()->get();
        $parts = Ex22Part::all();
        $modes = Ex23Mode::all();

        if ($names->isEmpty() || $types->isEmpty() || $parts->isEmpty() || $modes->isEmpty()) {
            session()->flash('error', 'Please ensure all exam configurations (Names, Types, Parts, Modes) are available.');
            return;
        }

        $count = 0;
        $existingCount = 0;
        $newCount = 0;

        DB::beginTransaction();
        try {
            foreach ($names as $name) {
                foreach ($types as $type) {
                    foreach ($parts as $part) {
                        foreach ($modes as $mode) {
                            $existing = Ex24Detail::where('exam_name_id', $name->id)
                                ->where('exam_type_id', $type->id)
                                ->where('exam_part_id', $part->id)
                                ->where('exam_mode_id', $mode->id)
                                ->where('session_id', $this->selected_session_id)
                                ->where('school_id', $this->selected_school_id)
                                ->first();

                            if (!$existing) {
                                Ex24Detail::create([
                                    'exam_name_id' => $name->id,
                                    'exam_type_id' => $type->id,
                                    'exam_part_id' => $part->id,
                                    'exam_mode_id' => $mode->id,
                                    'session_id' => $this->selected_session_id,
                                    'school_id' => $this->selected_school_id,
                                    'is_active' => 1,
                                    'remarks' => 'Auto-generated combination',
                                ]);
                                $newCount++;
                            } else {
                                $existingCount++;
                            }
                            $count++;
                        }
                    }
                }
            }

            DB::commit();
            
            $total = $names->count() * $types->count() * $parts->count() * $modes->count();
            session()->flash('success', 
                "Generated {$total} total combinations. New: {$newCount}, Existing: {$existingCount}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error generating combinations: ' . $e->getMessage());
        }
    }

    public function bulkDelete()
    {
        $this->validate([
            'selected_session_id' => 'required|exists:sessions,id',
            'selected_school_id' => 'required|exists:schools,id',
        ]);

        $deleted = Ex24Detail::where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->delete();

        session()->flash('message', "Deleted {$deleted} records successfully.");
    }

    public function toggleStatus($id)
    {
        $detail = Ex24Detail::findOrFail($id);
        $detail->update(['is_active' => !$detail->is_active]);
        session()->flash('message', 'Status updated successfully.');
    }
}
