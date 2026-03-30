<?php

namespace App\Http\Livewire;

use App\Models\Bs10Studentdb;
use App\Models\Bs11Studentcr;
use App\Models\Session;
use App\Models\School;
use App\Models\Bs04Myclass;
use App\Models\Bs05Semester;
use App\Models\Bs06Section;
use Livewire\Component;
use Livewire\WithPagination;

class Bs11StudentcrComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters
    public $selected_session_id;
    public $selected_school_id;
    public $search = '';
    public $perPage = 15;

    // Assign Roll Modal
    public $isAssignModalOpen = false;
    public $selected_student_id = null;
    public $use_custom_roll = false;
    public $custom_roll_no = '';
    public $selected_section_id = '';

    // Edit Roll Modal
    public $isEditRollModalOpen = false;
    public $editing_studentcr_id = null;
    public $edit_roll_no = '';
    public $editingStudentcr = null;

    // Dropdown options
    public $sessionOptions = [];
    public $schoolOptions = [];
    public $myclassOptions = [];
    public $semesterOptions = [];
    public $sectionOptions = [];

    // Computed property for next auto roll
    protected $next_auto_roll = 1;

    public function mount()
    {
        $this->loadOptions();
    }

    public function render()
    {
        $this->loadOptions();

        // Get students who are NOT in studentcr for the selected session
        $assignedStudentIds = Bs11Studentcr::where('session_id', $this->selected_session_id)
            ->pluck('studentdb_id')
            ->toArray();

        $query = Bs10Studentdb::with(['session', 'school', 'admissionMyclass', 'admissionSemester', 'admissionSection'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->whereNotIn('id', $assignedStudentIds);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%')
                    ->orWhere('student_code', 'like', '%' . $this->search . '%')
                    ->orWhere('father_name', 'like', '%' . $this->search . '%')
                    ->orWhere('aadhaar_number', 'like', '%' . $this->search . '%');
            });
        }

        $unassignedStudents = $query->orderBy('student_name', 'ASC')
            ->paginate($this->perPage);

        // Get already assigned students (studentcr records)
        $assignedStudents = Bs11Studentcr::with(['studentdb', 'studentdb.admissionMyclass', 'studentdb.admissionSection'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->orderBy('roll_no', 'ASC')
            ->paginate($this->perPage, ['*'], 'assigned_page');

        // Calculate next auto roll number
        $this->next_auto_roll = Bs11Studentcr::where('session_id', $this->selected_session_id)
            ->max('roll_no') + 1;

        return view('livewire.bs11-studentcr-comp', [
            'unassignedStudents' => $unassignedStudents,
            'assignedStudents' => $assignedStudents,
            'nextAutoRoll' => $this->next_auto_roll,
        ]);
    }

    public function loadOptions()
    {
        $this->sessionOptions = Session::active()->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $this->schoolOptions = School::active()->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        $this->myclassOptions = Bs04Myclass::active()->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        $this->semesterOptions = Bs05Semester::active()->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        $this->sectionOptions = Bs06Section::orderBy('name', 'ASC')->pluck('name', 'id')->toArray();

        // Set defaults
        if (!$this->selected_session_id && !empty($this->sessionOptions)) {
            $this->selected_session_id = array_key_first($this->sessionOptions);
        }
        if (!$this->selected_school_id && !empty($this->schoolOptions)) {
            $this->selected_school_id = array_key_first($this->schoolOptions);
        }
    }

    public function openAssignModal($studentId)
    {
        $this->selected_student_id = $studentId;
        $student = Bs10Studentdb::find($studentId);
        
        // Set default section from student's admission section
        $this->selected_section_id = $student->admission_section_id ?? '';
        
        // Reset other fields
        $this->use_custom_roll = false;
        $this->custom_roll_no = '';
        
        $this->isAssignModalOpen = true;
    }

    public function closeAssignModal()
    {
        $this->isAssignModalOpen = false;
        $this->selected_student_id = null;
        $this->use_custom_roll = false;
        $this->custom_roll_no = '';
        $this->selected_section_id = '';
    }

    public function assignRoll()
    {
        $this->validate([
            'selected_student_id' => 'required|exists:bs10_studentdbs,id',
            'selected_section_id' => 'nullable|exists:bs06_sections,id',
        ]);

        $student = Bs10Studentdb::find($this->selected_student_id);
        
        // Determine roll number - recalculate to get the latest max roll_no
        if ($this->use_custom_roll && $this->custom_roll_no) {
            $rollNo = $this->custom_roll_no;
            
            // Check if custom roll number already exists
            $exists = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->where('roll_no', $rollNo)
                ->exists();
            
            if ($exists) {
                session()->flash('error', 'Roll number already exists. Please choose a different number.');
                return;
            }
        } else {
            // Recalculate the next roll number to ensure we get the latest value
            $maxRollNo = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->max('roll_no');
            $rollNo = ($maxRollNo ?? 0) + 1;
        }

        // Create studentcr record
        Bs11Studentcr::create([
            'studentdb_id' => $this->selected_student_id,
            'roll_no' => $rollNo,
            'current_myclass_id' => $student->admission_myclass_id,
            'current_section_id' => $this->selected_section_id ?: $student->admission_section_id,
            'current_semester_id' => $student->admission_sememster_id,
            'current_session_id' => $this->selected_session_id,
            'session_id' => $this->selected_session_id,
            'school_id' => $this->selected_school_id,
            'is_active' => true,
        ]);

        session()->flash('message', "Roll number {$rollNo} assigned successfully.");
        $this->closeAssignModal();
        
        // Reset pagination to refresh the data
        $this->resetPage();
        $this->resetPage('assigned_page');
    }

    public function removeFromCr($studentcrId)
    {
        Bs11Studentcr::find($studentcrId)->delete();
        session()->flash('message', 'Student removed from Class Representative records.');
        
        // Reset pagination to refresh the data
        $this->resetPage();
        $this->resetPage('assigned_page');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedSessionId()
    {
        $this->resetPage();
    }

    public function updatingSelectedSchoolId()
    {
        $this->resetPage();
    }

    public function openEditRollModal($studentcrId)
    {
        $this->editing_studentcr_id = $studentcrId;
        $this->editingStudentcr = Bs11Studentcr::with('studentdb')->find($studentcrId);
        
        if ($this->editingStudentcr) {
            $this->edit_roll_no = $this->editingStudentcr->roll_no;
            $this->isEditRollModalOpen = true;
        }
    }

    public function closeEditRollModal()
    {
        $this->isEditRollModalOpen = false;
        $this->editing_studentcr_id = null;
        $this->edit_roll_no = '';
        $this->editingStudentcr = null;
    }

    public function updateRoll()
    {
        $this->validate([
            'edit_roll_no' => 'required|integer|min:1',
        ]);

        $studentcr = Bs11Studentcr::find($this->editing_studentcr_id);
        
        if (!$studentcr) {
            session()->flash('error', 'Student CR record not found.');
            $this->closeEditRollModal();
            return;
        }

        // Check if the new roll number already exists for a different student in the same session
        $exists = Bs11Studentcr::where('session_id', $studentcr->session_id)
            ->where('roll_no', $this->edit_roll_no)
            ->where('id', '!=', $this->editing_studentcr_id)
            ->exists();
        
        if ($exists) {
            session()->flash('error', 'Roll number already exists for another student. Please choose a different number.');
            return;
        }

        // Update the roll number
        $oldRollNo = $studentcr->roll_no;
        $studentcr->roll_no = $this->edit_roll_no;
        $studentcr->save();

        session()->flash('message', "Roll number updated from {$oldRollNo} to {$this->edit_roll_no} successfully.");
        $this->closeEditRollModal();
        
        // Reset pagination to refresh the data
        $this->resetPage('assigned_page');
    }
}
