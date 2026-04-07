<?php

namespace App\Http\Livewire;

use App\Models\Bs10Studentdb;
use App\Models\Bs12StudentdbSubject;
use App\Models\Bs07Subject;
use App\Models\Session;
use App\Models\School;
use App\Models\Bs04Myclass;
use App\Models\Bs05Semester;
use App\Models\Bs06Section;
use Livewire\Component;
use Livewire\WithPagination;

class Ex10StudentdbComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters
    public $selected_session_id;
    public $selected_school_id;
    public $search = '';
    public $perPage = 15;

    // Modal state
    public $isOpen = false;
    public $student_id;

    // Form fields
    public $student_code = '';
    public $student_name = '';
    public $father_name = '';
    public $mother_name = '';
    public $date_of_birth = '';
    public $gender = '';
    public $contact_number1 = '';
    public $contact_number2 = '';
    public $village = '';
    public $post_office = '';
    public $police_station = '';
    public $district = '';
    public $pin_code = '';
    public $nationality = '';
    public $caste = '';
    public $religion = '';
    public $admission_number = '';
    public $admission_date = '';
    public $board_reg_no = '';
    public $board_roll_no = '';
    public $admission_myclass_id = '';
    public $admission_section_id = '';
    public $admission_semester_id = '';
    public $admission_session_id = '';
    public $bank_account_number = '';
    public $bank_account_type = '';
    public $bank_name = '';
    public $bank_branch = '';
    public $bank_ifsc_code = '';
    public $birth_certificate_number = '';
    public $aadhaar_number = '';
    public $is_active = true;
    public $remarks = '';

    // Subject selection
    public $selected_subjects = [];
    public $available_subjects = [];

    // Dropdown options
    public $sessionOptions = [];
    public $schoolOptions = [];
    public $myclassOptions = [];
    public $semesterOptions = [];
    public $sectionOptions = [];
    public $genderOptions = ['Male', 'Female', 'Other'];
    public $bankAccountTypeOptions = ['Savings', 'Current'];

    protected function rules()
    {
        return [
            'student_name' => 'required|string|max:255',
            'session_id' => 'required|exists:sessions,id',
            'school_id' => 'required|exists:schools,id',
            'date_of_birth' => 'nullable|date',
            'admission_date' => 'nullable|date',
            'admission_myclass_id' => 'nullable|exists:bs04_myclasses,id',
            'admission_semester_id' => 'nullable|exists:bs05_semesters,id',
            'admission_section_id' => 'nullable|exists:bs06_sections,id',
            'admission_session_id' => 'nullable|exists:sessions,id',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->loadOptions();
    }

    public function Render()
    {
        $this->loadOptions();

        $query = Bs10Studentdb::with([
                'session', 'school', 
                'admissionMyclass', 'admissionSemester', 'admissionSection',
                'studentSubjects.subject'
            ])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%')
                    ->orWhere('board_reg_no', 'like', '%' . $this->search . '%')
                    ->orWhere('father_name', 'like', '%' . $this->search . '%')
                    ->orWhere('aadhaar_number', 'like', '%' . $this->search . '%');
            });
        }

        $students = $query->orderBy('board_reg_no', 'ASC')
            ->paginate($this->perPage);

        return view('livewire.ex10-studentdb-comp', [
            'students' => $students,
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

        // Load available subjects for the selected session and school
        $this->loadAvailableSubjects();
    }

    public function loadAvailableSubjects()
    {
        $this->available_subjects = Bs07Subject::where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->loadOptions();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $student = Bs10Studentdb::with('studentSubjects')->findOrFail($id);
        
        $this->student_id = $id;
        $this->student_code = $student->student_code ?? '';
        $this->student_name = $student->student_name ?? '';
        $this->father_name = $student->father_name ?? '';
        $this->mother_name = $student->mother_name ?? '';
        $this->date_of_birth = $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '';
        $this->gender = $student->gender ?? '';
        $this->contact_number1 = $student->contact_number1 ?? '';
        $this->contact_number2 = $student->contact_number2 ?? '';
        $this->village = $student->village ?? '';
        $this->post_office = $student->post_office ?? '';
        $this->police_station = $student->police_station ?? '';
        $this->district = $student->district ?? '';
        $this->pin_code = $student->pin_code ?? '';
        $this->nationality = $student->nationality ?? '';
        $this->caste = $student->caste ?? '';
        $this->religion = $student->religion ?? '';
        $this->admission_number = $student->admission_number ?? '';
        $this->admission_date = $student->admission_date ? $student->admission_date->format('Y-m-d') : null;
        $this->board_reg_no = $student->board_reg_no ?? '';
        $this->board_roll_no = $student->board_roll_no ?? '';
        $this->admission_myclass_id = $student->admission_myclass_id ?? '';
        $this->admission_section_id = $student->admission_section_id ?? '';
        $this->admission_semester_id = $student->admission_semester_id ?? '';
        $this->admission_session_id = $student->admission_session_id ?? '';
        $this->bank_account_number = $student->bank_account_number ?? '';
        $this->bank_account_type = $student->bank_account_type ?? '';
        $this->bank_name = $student->bank_name ?? '';
        $this->bank_branch = $student->bank_branch ?? '';
        $this->bank_ifsc_code = $student->bank_ifsc_code ?? '';
        $this->birth_certificate_number = $student->birth_certificate_number ?? '';
        $this->aadhaar_number = $student->aadhaar_number ?? '';
        $this->is_active = $student->is_active ?? true;
        $this->remarks = $student->remarks ?? '';

        // Load selected subjects
        $this->selected_subjects = $student->studentSubjects->pluck('subject_id')->toArray();

        $this->loadOptions();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->student_id = null;
        $this->student_code = '';
        $this->student_name = '';
        $this->father_name = '';
        $this->mother_name = '';
        $this->date_of_birth = '';
        $this->gender = '';
        $this->contact_number1 = '';
        $this->contact_number2 = '';
        $this->village = '';
        $this->post_office = '';
        $this->police_station = '';
        $this->district = '';
        $this->pin_code = '';
        $this->nationality = '';
        $this->caste = '';
        $this->religion = '';
        $this->admission_number = '';
        $this->admission_date = '';
        $this->board_reg_no = '';
        $this->board_roll_no = '';
        $this->admission_myclass_id = '';
        $this->admission_section_id = '';
        $this->admission_semester_id = '';
        $this->admission_session_id = '';
        $this->bank_account_number = '';
        $this->bank_account_type = '';
        $this->bank_name = '';
        $this->bank_branch = '';
        $this->bank_ifsc_code = '';
        $this->birth_certificate_number = '';
        $this->aadhaar_number = '';
        $this->is_active = true;
        $this->remarks = '';
        $this->selected_subjects = [];
    }

    public function store()
    {
        $this->validate([
            'student_name' => 'required|string|max:255',
            'selected_session_id' => 'required|exists:sessions,id',
            'selected_school_id' => 'required|exists:schools,id',
        ]);

        $data = [
            'student_code' => $this->student_code,
            'student_name' => $this->student_name,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'contact_number1' => $this->contact_number1,
            'contact_number2' => $this->contact_number2,
            'village' => $this->village,
            'post_office' => $this->post_office,
            'police_station' => $this->police_station,
            'district' => $this->district,
            'pin_code' => $this->pin_code,
            'nationality' => $this->nationality,
            'caste' => $this->caste,
            'religion' => $this->religion,
            'admission_number' => $this->admission_number,
            'admission_date' => $this->admission_date,
            'board_reg_no' => $this->board_reg_no,
            'board_roll_no' => $this->board_roll_no,
            'admission_myclass_id' => $this->admission_myclass_id ?: null,
            'admission_section_id' => $this->admission_section_id ?: null,
            'admission_semester_id' => $this->admission_semester_id ?: null,
            'admission_session_id' => $this->admission_session_id ?: null,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_type' => $this->bank_account_type,
            'bank_name' => $this->bank_name,
            'bank_branch' => $this->bank_branch,
            'bank_ifsc_code' => $this->bank_ifsc_code,
            'birth_certificate_number' => $this->birth_certificate_number,
            'aadhaar_number' => $this->aadhaar_number,
            'session_id' => $this->selected_session_id,
            'school_id' => $this->selected_school_id,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ];

        $student = Bs10Studentdb::updateOrCreate(['id' => $this->student_id], $data);

        // Sync subjects
        $this->syncStudentSubjects($student->id);

        session()->flash('message', $this->student_id ? 'Student updated successfully.' : 'Student created successfully.');

        $this->closeModal();
    }

    private function syncStudentSubjects($studentId)
    {
        // Remove existing subjects for this student
        Bs12StudentdbSubject::where('studentdb_id', $studentId)->delete();

        // Add selected subjects
        foreach ($this->selected_subjects as $subjectId) {
            Bs12StudentdbSubject::create([
                'studentdb_id' => $studentId,
                'subject_id' => $subjectId,
                'is_active' => 1,
            ]);
        }
    }

    public function delete($id)
    {
        // Delete student subjects first
        Bs12StudentdbSubject::where('studentdb_id', $id)->delete();
        
        // Delete student
        Bs10Studentdb::find($id)->delete();
        session()->flash('message', 'Student deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $student = Bs10Studentdb::find($id);
        $student->update(['is_active' => !$student->is_active]);
        session()->flash('message', 'Status updated successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedSessionId()
    {
        $this->resetPage();
        $this->loadAvailableSubjects();
    }

    public function updatingSelectedSchoolId()
    {
        $this->resetPage();
        $this->loadAvailableSubjects();
    }
}
