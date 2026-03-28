<?php

namespace App\Http\Livewire;

use App\Models\Bs11Studentcr;
use App\Models\Bs12StudentdbSubject;
use App\Models\Bs10Studentdb;
use App\Models\Session;
use App\Models\School;
use App\Models\Bs04Myclass;
use App\Models\Bs05Semester;
use App\Models\Bs06Section;
use App\Models\Bs07Subject;
use App\Models\Ex30MarksEntry;
use App\Models\Ex26MarksGrade;
use App\Models\Ex24Detail;
use App\Models\Ex25Settings;
use Livewire\Component;

class Ex30MarksEntryComp2 extends Component
{
    // Filters
    public $selected_session_id;
    public $selected_school_id;
    public $selected_myclass_id;
    public $selected_section_id;
    public $selected_semester_id;
    public $selected_exam_detail_id;
    public $selected_subject_id;
    public $full_mark = 100;

    // Marks data
    public $marksData = [];
    public $studentList = [];
    
    // Track which fields have been modified
    protected $listeners = [];

    // Dropdown options
    public $sessionOptions = [];
    public $schoolOptions = [];
    public $myclassOptions = [];
    public $sectionOptions = [];
    public $semesterOptions = [];
    public $examDetailOptions = [];
    public $subjectOptions = [];
    
    // Selected exam detail info
    public $selectedExamDetail = null;

    public function mount()
    {
        $this->loadOptions();
    }

    public function render()
    {
        $this->loadOptions();
        $this->loadStudents();

        return view('livewire.ex30-marks-entry-comp2');
    }

    public function loadOptions()
    {
        // Session options (only active)
        $this->sessionOptions = Session::active()
            ->orderBy('name', 'DESC')
            ->pluck('name', 'id')
            ->toArray();

        // School options (only active)
        $this->schoolOptions = School::active()
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        // MyClass options (based on session - filter from studentcr)
        if ($this->selected_session_id) {
            $myclassIds = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->where('is_active', 1)
                ->whereNotNull('current_myclass_id')
                ->distinct()
                ->pluck('current_myclass_id');
            
            $this->myclassOptions = Bs04Myclass::whereIn('id', $myclassIds)
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $this->myclassOptions = [];
        }

        // Section options (based on selected myclass from studentcr)
        if ($this->selected_session_id && $this->selected_myclass_id) {
            $sectionIds = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->where('current_myclass_id', $this->selected_myclass_id)
                ->where('is_active', 1)
                ->whereNotNull('current_section_id')
                ->distinct()
                ->pluck('current_section_id');
            
            $this->sectionOptions = Bs06Section::whereIn('id', $sectionIds)
                ->orderBy('name', 'ASC')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $this->sectionOptions = [];
        }

        // Semester options (based on selected myclass from studentcr)
        if ($this->selected_session_id && $this->selected_myclass_id) {
            $semesterIds = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->where('current_myclass_id', $this->selected_myclass_id)
                ->where('is_active', 1)
                ->whereNotNull('current_semester_id')
                ->distinct()
                ->pluck('current_semester_id');
            
            $this->semesterOptions = Bs05Semester::whereIn('id', $semesterIds)
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $this->semesterOptions = [];
        }

        // Exam Detail options - based on session, school, myclass, and semester
        if ($this->selected_session_id && $this->selected_myclass_id && $this->selected_semester_id) {
            $examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
                ->where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id)
                ->where('is_active', 1)
                ->get();
            
            $this->examDetailOptions = [];
            foreach ($examDetails as $detail) {
                $label = sprintf('%s | %s | %s | %s',
                    $detail->examName->name ?? 'N/A',
                    $detail->examType->name ?? 'N/A',
                    $detail->examPart->name ?? 'N/A',
                    $detail->examMode->name ?? 'N/A'
                );
                $this->examDetailOptions[$detail->id] = $label;
            }
        } else {
            $this->examDetailOptions = [];
        }

        // Subject options - based on session AND myclass (from studentdb subjects)
        if ($this->selected_session_id && $this->selected_myclass_id) {
            // Get studentdb_ids from studentcr for this session and myclass
            $studentcrIds = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->where('current_myclass_id', $this->selected_myclass_id)
                ->where('is_active', 1)
                ->pluck('id');

            $studentDbIds = Bs11Studentcr::whereIn('id', $studentcrIds)
                ->pluck('studentdb_id')
                ->toArray();

            // Get subject_ids from studentdb_subjects for these students
            $subjectIds = Bs12StudentdbSubject::whereIn('studentdb_id', $studentDbIds)
                ->distinct()
                ->pluck('subject_id');

            // Get subjects from the subjects table
            $this->subjectOptions = Bs07Subject::whereIn('id', $subjectIds)
                ->where('session_id', $this->selected_session_id)
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $this->subjectOptions = [];
        }

        // Set defaults
        if (!$this->selected_session_id && !empty($this->sessionOptions)) {
            $this->selected_session_id = array_key_first($this->sessionOptions);
        }
        if (!$this->selected_school_id && !empty($this->schoolOptions)) {
            $this->selected_school_id = array_key_first($this->schoolOptions);
        }
    }

    private function loadStudents()
    {
        $this->studentList = [];
        $this->marksData = [];
        $this->selectedExamDetail = null;

        if (!$this->selected_session_id || !$this->selected_myclass_id || !$this->selected_semester_id || !$this->selected_exam_detail_id || !$this->selected_subject_id) {
            return;
        }
        
        // Load selected exam detail info
        $this->selectedExamDetail = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
            ->find($this->selected_exam_detail_id);

        // Get studentdb_ids that have the selected subject
        $studentDbIdsWithSubject = Bs12StudentdbSubject::where('subject_id', $this->selected_subject_id)
            ->pluck('studentdb_id')
            ->toArray();

        if (empty($studentDbIdsWithSubject)) {
            return;
        }

        // Get studentcr records matching session, myclass, semester, and having the subject
        $query = Bs11Studentcr::with(['studentdb'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('current_myclass_id', $this->selected_myclass_id)
            ->where('current_semester_id', $this->selected_semester_id)
            ->whereIn('studentdb_id', $studentDbIdsWithSubject)
            ->where('is_active', 1);

        // Filter by section if selected
        if ($this->selected_section_id) {
            $query->where('current_section_id', $this->selected_section_id);
        }

        $students = $query->orderBy('roll_no', 'ASC')->get();

        // Get existing marks for this subject/session/school/exam_detail
        $existingMarks = Ex30MarksEntry::where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('subject_id', $this->selected_subject_id)
            ->where('exam_detail_id', $this->selected_exam_detail_id)
            ->get()
            ->keyBy('studentcr_id');

        foreach ($students as $studentcr) {
            $studentDb = $studentcr->studentdb;
            
            $this->studentList[] = [
                'id' => $studentcr->id,
                'studentcr_id' => $studentcr->id,
                'studentdb_id' => $studentcr->studentdb_id,
                'name' => $studentDb ? ($studentDb->student_name ?? 'N/A') : 'N/A',
                'roll_no' => $studentcr->roll_no,
                'myclass_id' => $studentcr->current_myclass_id,
                'section_id' => $studentcr->current_section_id,
                'semester_id' => $studentcr->current_semester_id,
            ];

            $existing = $existingMarks->get($studentcr->id);
            
            // Initialize with proper values - use empty string for better form handling
            $this->marksData[$studentcr->id] = [
                'marks_obtained' => $existing && $existing->marks_obtained !== null ? (string)$existing->marks_obtained : '',
                'marks_entry_id' => $existing ? $existing->id : null,
            ];
        }
    }

    public function saveMarks()
    {
        if (!$this->selected_session_id || !$this->selected_myclass_id || !$this->selected_semester_id || !$this->selected_exam_detail_id || !$this->selected_subject_id) {
            session()->flash('error', 'Please select session, class, semester, exam detail, and subject.');
            return;
        }

        $savedCount = 0;

        foreach ($this->studentList as $student) {
            $studentcrId = $student['id'];
            $data = $this->marksData[$studentcrId] ?? null;
            
            // Skip if no data or marks_obtained is not set
            if (!$data || !isset($data['marks_obtained'])) {
                continue;
            }
            
            // Trim and check if empty
            $marksValue = trim((string)$data['marks_obtained']);
            if ($marksValue === '') {
                continue;
            }

            $marksObtained = (float) $marksValue;
            
            // Validate marks range
            if ($marksObtained < 0 || $marksObtained > $this->full_mark) {
                continue;
            }
            $percentage = $this->full_mark > 0 ? round(($marksObtained / $this->full_mark) * 100, 2) : 0;
            $grade = $this->calculateGrade($percentage);

            $dataToSave = [
                'studentcr_id' => $studentcrId,
                'studentdb_id' => $student['studentdb_id'],
                'myclass_id' => $student['myclass_id'],
                'section_id' => $student['section_id'],
                'semester_id' => $student['semester_id'],
                'subject_id' => $this->selected_subject_id,
                'exam_detail_id' => $this->selected_exam_detail_id,
                'exam_setting_id' => null,
                'marks_obtained' => $marksObtained,
                'marks_percentage' => $percentage,
                'marks_grade' => $grade,
                'session_id' => $this->selected_session_id,
                'school_id' => $this->selected_school_id,
                'is_active' => 1,
            ];

            if (isset($data['marks_entry_id']) && $data['marks_entry_id']) {
                Ex30MarksEntry::where('id', $data['marks_entry_id'])->update($dataToSave);
            } else {
                $existing = Ex30MarksEntry::where('studentcr_id', $studentcrId)
                    ->where('subject_id', $this->selected_subject_id)
                    ->where('exam_detail_id', $this->selected_exam_detail_id)
                    ->where('session_id', $this->selected_session_id)
                    ->where('school_id', $this->selected_school_id)
                    ->first();

                if ($existing) {
                    $existing->update($dataToSave);
                } else {
                    Ex30MarksEntry::create($dataToSave);
                }
            }

            $savedCount++;
        }

        session()->flash('message', "Successfully saved {$savedCount} marks.");
        
        // Reload students to reflect changes
        $this->loadStudents();
    }

    private function calculateGrade($percentage)
    {
        // Try to get grade from settings
        $gradeSetting = Ex26MarksGrade::where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('percentage_from', '<=', $percentage)
            ->where('percentage_to', '>=', $percentage)
            ->first();

        if ($gradeSetting) {
            return $gradeSetting->grade;
        }

        // Default grade calculation
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    public function updatedSelectedSessionId()
    {
        $this->selected_myclass_id = '';
        $this->selected_section_id = '';
        $this->selected_semester_id = '';
        $this->selected_exam_detail_id = '';
        $this->selected_subject_id = '';
        $this->studentList = [];
        $this->marksData = [];
    }

    public function updatedSelectedMyclassId()
    {
        $this->selected_section_id = '';
        $this->selected_semester_id = '';
        $this->selected_exam_detail_id = '';
        $this->selected_subject_id = '';
        $this->loadStudents();
    }

    public function updatedSelectedSectionId()
    {
        $this->loadStudents();
    }

    public function updatedSelectedSemesterId()
    {
        $this->selected_exam_detail_id = '';
        $this->selected_subject_id = '';
        $this->loadStudents();
    }
    
    public function updatedSelectedExamDetailId()
    {
        $this->loadStudents();
    }

    public function updatedSelectedSubjectId()
    {
        $this->loadStudents();
    }
}
