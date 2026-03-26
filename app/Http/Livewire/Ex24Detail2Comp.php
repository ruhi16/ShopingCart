<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ex24Detail;
use App\Models\Bs09MyclassSemester;
use App\Models\Ex20Name;
use App\Models\Ex21Type;
use App\Models\Ex22Part;
use App\Models\Ex23Mode;
use App\Models\Session;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class Ex24Detail2Comp extends Component
{
    public $myclassSemesters, $search = '';
    public $selected_session_id, $selected_school_id;
    public $sessions, $schools;
    
    // Modal properties
    public $isOpen = 0;
    public $selectedMyclassSemesterId;
    public $selectedMyclassName;
    public $selectedSemesterName;
    
    // Exam configuration
    public $examConfigurations = [];
    
    // Edit mode properties
    public $isEditModalOpen = 0;
    public $editingDetailId;
    public $editExamNameId;
    public $editExamTypeId;
    public $editExamPartId;
    public $editExamModeId;
    public $editIsActive;
    public $editRemarks;
    
    public function render()
    {
        $this->loadDropdownData();
        
        // Get all class-semester combinations ordered by myclass_id then semester_id
        $query = Bs09MyclassSemester::with(['myclass', 'semester', 'session', 'school'])
            ->where('school_id', $this->selected_school_id)
            ->where('session_id', $this->selected_session_id);
        
        if ($this->search) {
            $query->whereHas('myclass', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }
        
        $this->myclassSemesters = $query->orderBy('myclass_id', 'asc')
            ->orderBy('semester_id', 'asc')
            ->get();
        
        return view('livewire.ex24-detail2-comp')
            // ->layout('livewire.lc-main-layout')
            ;
    }

    private function loadDropdownData()
    {
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

    /**
     * Open modal to configure exam details for a specific class-semester combination
     */
    public function configureExam($myclassSemesterId)
    {
        $myclassSemester = Bs09MyclassSemester::findOrFail($myclassSemesterId);
        
        $this->selectedMyclassSemesterId = $myclassSemesterId;
        $this->selectedMyclassName = $myclassSemester->myclass->name ?? 'N/A';
        $this->selectedSemesterName = $myclassSemester->semester->name ?? 'N/A';
        $this->selected_session_id = $myclassSemester->session_id;
        $this->selected_school_id = $myclassSemester->school_id;
        
        // Initialize with one empty configuration row
        $this->examConfigurations = [
            [
                'exam_name_id' => '',
                'exam_type_id' => '',
                'exam_parts' => [], // Multiple parts can be selected
            ]
        ];
        
        $this->isOpen = true;
    }

    /**
     * Add a new exam name configuration row
     */
    public function addExamNameRow()
    {
        $this->examConfigurations[] = [
            'exam_name_id' => '',
            'exam_type_id' => '',
            'exam_parts' => [],
        ];
    }

    /**
     * Remove an exam name configuration row
     */
    public function removeExamNameRow($index)
    {
        if (count($this->examConfigurations) > 1) {
            unset($this->examConfigurations[$index]);
            $this->examConfigurations = array_values($this->examConfigurations); // Re-index
        }
    }

    /**
     * Add a part to a specific exam configuration
     */
    public function addPartToExam($configIndex)
    {
        $this->examConfigurations[$configIndex]['exam_parts'][] = [
            'part_id' => '',
            'mode_id' => '',
        ];
    }

    /**
     * Remove a part from a specific exam configuration
     */
    public function removePartFromExam($configIndex, $partIndex)
    {
        if (isset($this->examConfigurations[$configIndex]['exam_parts'][$partIndex])) {
            unset($this->examConfigurations[$configIndex]['exam_parts'][$partIndex]);
            $this->examConfigurations[$configIndex]['exam_parts'] = 
                array_values($this->examConfigurations[$configIndex]['exam_parts']); // Re-index
        }
    }

    /**
     * Save all exam configurations
     */
    public function saveExamConfigurations()
    {
        $this->validate([
            'selectedMyclassSemesterId' => 'required|exists:bs09_myclass_semesters,id',
        ]);

        // Get the myclass and semester IDs from the selected combination
        $myclassSemester = Bs09MyclassSemester::findOrFail($this->selectedMyclassSemesterId);
        $myclassId = $myclassSemester->myclass_id;
        $semesterId = $myclassSemester->semester_id;

        // Validate each configuration
        foreach ($this->examConfigurations as $index => $config) {
            $this->validate([
                "examConfigurations.{$index}.exam_name_id" => 'required|exists:ex20_names,id',
                "examConfigurations.{$index}.exam_type_id" => 'required|exists:ex21_types,id',
            ], [
                "examConfigurations.{$index}.exam_name_id.required" => 'Exam Name is required.',
                "examConfigurations.{$index}.exam_type_id.required" => 'Exam Type is required.',
            ]);

            // Validate each part in the configuration
            foreach ($config['exam_parts'] as $partIndex => $part) {
                $this->validate([
                    "examConfigurations.{$index}.exam_parts.{$partIndex}.part_id" => 'required|exists:ex22_parts,id',
                    "examConfigurations.{$index}.exam_parts.{$partIndex}.mode_id" => 'required|exists:ex23_modes,id',
                ], [
                    "examConfigurations.{$index}.exam_parts.{$partIndex}.part_id.required" => 'Exam Part is required.',
                    "examConfigurations.{$index}.exam_parts.{$partIndex}.mode_id.required" => 'Exam Mode is required.',
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->examConfigurations as $config) {
                // Create combinations for each part
                foreach ($config['exam_parts'] as $part) {
                    $existing = Ex24Detail::where('exam_name_id', $config['exam_name_id'])
                        ->where('exam_type_id', $config['exam_type_id'])
                        ->where('exam_part_id', $part['part_id'])
                        ->where('exam_mode_id', $part['mode_id'])
                        ->where('session_id', $this->selected_session_id)
                        ->where('school_id', $this->selected_school_id)
                        ->where('myclass_id', $myclassId)
                        ->where('semester_id', $semesterId)
                        ->first();

                    if (!$existing) {
                        Ex24Detail::create([
                            'exam_name_id' => $config['exam_name_id'],
                            'exam_type_id' => $config['exam_type_id'],
                            'exam_part_id' => $part['part_id'],
                            'exam_mode_id' => $part['mode_id'],
                            'session_id' => $this->selected_session_id,
                            'school_id' => $this->selected_school_id,
                            'myclass_id' => $myclassId,
                            'semester_id' => $semesterId,
                            'is_active' => 1,
                            'remarks' => "Class/Semester: {$this->selectedMyclassName}/{$this->selectedSemesterName}",
                        ]);
                        $savedCount++;
                    }
                }
            }

            DB::commit();
            
            session()->flash('success', "Successfully saved {$savedCount} exam detail combinations.");
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving configurations: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->examConfigurations = [];
        $this->selectedMyclassSemesterId = null;
    }

    public function deleteClassSemester($id)
    {
        Bs09MyclassSemester::find($id)->delete();
        session()->flash('message', 'Class-Semester combination deleted successfully.');
    }

    public function toggleExamDetailStatus($id)
    {
        $detail = Ex24Detail::findOrFail($id);
        $detail->update(['is_active' => !$detail->is_active]);
        session()->flash('message', 'Exam detail status updated successfully.');
    }

    public function deleteExamDetail($id)
    {
        Ex24Detail::find($id)->delete();
        session()->flash('message', 'Exam detail deleted successfully.');
    }

    public function editExamDetail($id)
    {
        $detail = Ex24Detail::findOrFail($id);
        
        $this->editingDetailId = $id;
        $this->editExamNameId = $detail->exam_name_id;
        $this->editExamTypeId = $detail->exam_type_id;
        $this->editExamPartId = $detail->exam_part_id;
        $this->editExamModeId = $detail->exam_mode_id;
        $this->editIsActive = $detail->is_active;
        $this->editRemarks = $detail->remarks ?? '';
        
        $this->isEditModalOpen = true;
    }

    public function updateExamDetail()
    {
        $this->validate([
            'editExamNameId' => 'required|exists:ex20_names,id',
            'editExamTypeId' => 'required|exists:ex21_types,id',
            'editExamPartId' => 'required|exists:ex22_parts,id',
            'editExamModeId' => 'required|exists:ex23_modes,id',
        ]);

        $detail = Ex24Detail::findOrFail($this->editingDetailId);
        $detail->update([
            'exam_name_id' => $this->editExamNameId,
            'exam_type_id' => $this->editExamTypeId,
            'exam_part_id' => $this->editExamPartId,
            'exam_mode_id' => $this->editExamModeId,
            'is_active' => $this->editIsActive,
            'remarks' => $this->editRemarks,
        ]);

        session()->flash('message', 'Exam detail updated successfully.');
        $this->closeEditModal();
    }

    public function closeEditModal()
    {
        $this->isEditModalOpen = false;
        $this->editingDetailId = null;
        $this->editExamNameId = null;
        $this->editExamTypeId = null;
        $this->editExamPartId = null;
        $this->editExamModeId = null;
        $this->editIsActive = null;
        $this->editRemarks = null;
    }
}
