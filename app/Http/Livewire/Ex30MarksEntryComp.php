<?php

namespace App\Http\Livewire;

use App\Models\Ex30MarksEntry;
use App\Models\Ex25Settings;
use App\Models\Ex24Detail;
use App\Models\Bs09MyclassSemester;
use App\Models\Bs11Studentcr;
use App\Models\Session;
use App\Models\School;
use App\Models\Ex26MarksGrade;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Ex30MarksEntryComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters
    public $selected_session_id;
    public $selected_school_id;
    public $selected_myclass_id;
    public $selected_semester_id;
    public $selected_exam_detail_id;
    public $selected_subject_id;
    public $search = '';
    public $perPage = 10;

    // Modal state
    public $isOpen = false;
    public $isViewMode = false;

    // Current selection for marks entry
    public $currentSettingId;
    public $currentExamDetailId;
    public $currentMyclassId;
    public $currentSemesterId;
    public $currentSectionId;
    public $currentSubjectId;
    public $currentSubjectName;
    public $currentFullMark;
    public $currentPassMark;
    
    // Dropdown options
    public $myclassOptions = [];
    public $semesterOptions = [];
    public $examDetailOptions = [];
    public $subjectOptions = [];
    public $availableSettings = [];

    // Marks entry data
    public $marksData = [];
    public $studentList = [];

    // View mode data
    public $viewingStudentId;
    public $viewingMarks = [];

    // Dropdown options
    public $sessionOptions = [];
    public $schoolOptions = [];

    protected function rules()
    {
        return [
            'selected_session_id' => 'required|exists:sessions,id',
            'selected_school_id' => 'required|exists:schools,id',
        ];
    }

    public function mount()
    {
        $this->loadOptions();
    }

    public function render()
    {
        $this->loadOptions();

        // Get available exam settings based on selections
        $query = Ex25Settings::with(['myclass', 'semester', 'examDetail.examName', 'examDetail.examType', 'examDetail.examPart', 'examDetail.examMode', 'subject'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1);

        // Filter by selected class if set
        if ($this->selected_myclass_id) {
            $query->where('myclass_id', $this->selected_myclass_id);
        }

        // Filter by selected semester if set
        if ($this->selected_semester_id) {
            $query->where('semester_id', $this->selected_semester_id);
        }

        // Filter by selected exam detail if set
        if ($this->selected_exam_detail_id) {
            $query->where('exam_detail_id', $this->selected_exam_detail_id);
        }

        // Filter by selected subject if set
        if ($this->selected_subject_id) {
            $query->where('subject_id', $this->selected_subject_id);
        }

        $settings = $query->orderBy('myclass_id', 'asc')
            ->orderBy('semester_id', 'asc')
            ->orderBy('exam_detail_id', 'asc')
            ->orderBy('subject_id', 'asc')
            ->paginate($this->perPage);

        // Group settings by myclass-semester-examdetail-subject
        $groupedSettings = [];
        foreach ($settings as $setting) {
            $key = $setting->myclass_id . '-' . $setting->semester_id . '-' . $setting->exam_detail_id . '-' . $setting->subject_id;
            if (!isset($groupedSettings[$key])) {
                $groupedSettings[$key] = [
                    'myclass' => $setting->myclass,
                    'semester' => $setting->semester,
                    'exam_detail' => $setting->examDetail,
                    'subject' => $setting->subject,
                    'setting' => $setting,
                ];
            }
        }

        return view('livewire.ex30-marks-entry-comp', [
            'groupedSettings' => $groupedSettings,
            'settings' => $settings,
        ]);
    }

    public function loadOptions()
    {
        $this->sessionOptions = Session::active()->orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
        $this->schoolOptions = School::active()->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();

        // Set defaults
        if (!$this->selected_session_id && !empty($this->sessionOptions)) {
            $this->selected_session_id = array_key_first($this->sessionOptions);
        }
        if (!$this->selected_school_id && !empty($this->schoolOptions)) {
            $this->selected_school_id = array_key_first($this->schoolOptions);
        }
        
        // Load class-semester combinations that have settings
        $this->loadMyclassSemesterOptions();
        
        // Load exam details based on selected class-semester
        if ($this->selected_myclass_id && $this->selected_semester_id) {
            $this->loadExamDetailOptions();
        }
        
        // Load subjects based on selected exam detail
        if ($this->selected_exam_detail_id) {
            $this->loadSubjectOptions();
        }
    }
    
    private function loadMyclassSemesterOptions()
    {
        // Get unique myclass-semester combinations from Ex25Settings
        $settings = Ex25Settings::select('myclass_id', 'semester_id')
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->distinct()
            ->get();
        
        $this->myclassOptions = [];
        $this->semesterOptions = [];
        
        foreach ($settings as $setting) {
            $this->myclassOptions[$setting->myclass_id] = $setting->myclass ? $setting->myclass->name : 'Class ' . $setting->myclass_id;
            $this->semesterOptions[$setting->semester_id] = $setting->semester ? $setting->semester->name : 'Semester ' . $setting->semester_id;
        }
    }
    
    public function updatedSelectedMyclassId()
    {
        $this->selected_exam_detail_id = null;
        $this->selected_subject_id = null;
        $this->resetPage();
    }
    
    public function updatedSelectedSemesterId()
    {
        $this->selected_exam_detail_id = null;
        $this->selected_subject_id = null;
        $this->resetPage();
    }
    
    private function loadExamDetailOptions()
    {
        // Get exam details for this class-semester from Ex24Detail
        $examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
            ->where('myclass_id', $this->selected_myclass_id)
            ->where('semester_id', $this->selected_semester_id)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->get();
        
        $this->examDetailOptions = [];
        foreach ($examDetails as $detail) {
            $label = '';
            if ($detail->examName) $label .= $detail->examName->name . ' - ';
            if ($detail->examType) $label .= $detail->examType->name . ' - ';
            if ($detail->examPart) $label .= $detail->examPart->name . ' - ';
            if ($detail->examMode) $label .= $detail->examMode->name;
            
            $this->examDetailOptions[$detail->id] = trim($label, ' - ');
        }
    }
    
    public function updatedSelectedExamDetailId()
    {
        $this->selected_subject_id = null;
        $this->resetPage();
    }
    
    private function loadSubjectOptions()
    {
        // Get subjects for this exam detail from Ex25Settings
        $settings = Ex25Settings::with(['subject'])
            ->where('myclass_id', $this->selected_myclass_id)
            ->where('semester_id', $this->selected_semester_id)
            ->where('exam_detail_id', $this->selected_exam_detail_id)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->get();
        
        $this->subjectOptions = [];
        foreach ($settings as $setting) {
            if ($setting->subject) {
                $this->subjectOptions[$setting->subject_id] = $setting->subject->name;
            }
        }
    }

    /**
     * Open marks entry modal for a specific subject
     */
    public function openMarksEntry($settingId)
    {
        $setting = Ex25Settings::with(['myclass', 'semester', 'examDetail', 'subject'])
            ->findOrFail($settingId);

        $this->currentSettingId = $setting->id;
        $this->currentExamDetailId = $setting->exam_detail_id;
        $this->currentMyclassId = $setting->myclass_id;
        $this->currentSemesterId = $setting->semester_id;
        $this->currentSectionId = $setting->section_id;
        $this->currentSubjectId = $setting->subject_id;
        $this->currentSubjectName = $setting->subject->name ?? 'N/A';
        $this->currentFullMark = $setting->full_mark;
        $this->currentPassMark = $setting->pass_mark;

        // Get students for this class/semester/section
        $this->loadStudentMarks();

        $this->isOpen = true;
        $this->isViewMode = false;
    }

    /**
     * Load student marks data
     */
    private function loadStudentMarks()
    {
        // Get students from studentcr table with studentdb relationship
        $query = Bs11Studentcr::with(['studentdb'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('current_myclass_id', $this->currentMyclassId)
            ->where('current_sememster_id', $this->currentSemesterId) // Note: typo in original migration
            ->where('is_active', 1);

        if ($this->currentSectionId) {
            $query->where('current_section_id', $this->currentSectionId);
        }

        $students = $query->orderBy('roll_no', 'asc')->get();

        // Get existing marks
        $existingMarks = Ex30MarksEntry::where('exam_setting_id', $this->currentSettingId)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->get()
            ->keyBy('studentcr_id');

        $this->marksData = [];
        $this->studentList = [];

        foreach ($students as $studentcr) {
            $existing = $existingMarks->get($studentcr->id);
            $marksObtained = $existing ? $existing->marks_obtained : null;
            $percentage = $marksObtained !== null ? round(($marksObtained / $this->currentFullMark) * 100, 2) : null;
            $grade = $percentage !== null ? $this->getGrade($percentage) : null;

            // Get student details from studentdb
            $studentDb = $studentcr->studentdb;
            
            // Student name is in 'student_name' column (not 'name')
            $studentName = $studentDb ? ($studentDb->student_name ?? 'N/A') : 'N/A';
            
            // Roll number from studentcr
            $rollNo = $studentcr->roll_no;
            
            // Class, Section, Semester from studentcr's current_* columns
            // (Or from studentdb's admission_* columns if needed)
            $myclassId = $studentcr->current_myclass_id ?? ($studentDb ? $studentDb->admission_myclass_id : null);
            $sectionId = $studentcr->current_section_id ?? ($studentDb ? $studentDb->admission_section_id : null);
            $semesterId = $studentcr->current_sememster_id ?? ($studentDb ? $studentDb->admission_sememster_id : null);

            $this->studentList[] = [
                'id' => $studentcr->id,
                'studentcr_id' => $studentcr->id,
                'name' => $studentName,
                'roll_no' => $rollNo,
                'myclass_id' => $myclassId,
                'section_id' => $sectionId,
                'semester_id' => $semesterId,
                'studentdb_id' => $studentcr->studentdb_id,
            ];

            $this->marksData[$studentcr->id] = [
                'marks_obtained' => $marksObtained,
                'percentage' => $percentage,
                'grade' => $grade,
                'marks_entry_id' => $existing ? $existing->id : null,
            ];
        }
    }

    /**
     * Debug method to check student data
     */
    public function debugStudentData()
    {
        $students = Bs11Studentcr::with(['studentdb'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->limit(5)
            ->get();

        foreach ($students as $student) {
            \Log::info('StudentCR Debug', [
                'id' => $student->id,
                'studentdb_id' => $student->studentdb_id,
                'roll_no' => $student->roll_no,
                'current_myclass_id' => $student->current_myclass_id,
                'current_sememster_id' => $student->current_sememster_id,
                'studentdb_name' => $student->studentdb ? $student->studentdb->name : 'NULL',
            ]);
        }

        session()->flash('message', 'Debug info logged. Check Laravel logs.');
    }

    /**
     * Get grade based on percentage
     */
    private function getGrade($percentage)
    {
        $grade = Ex26MarksGrade::where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('percentage_from', '<=', $percentage)
            ->where('percentage_to', '>=', $percentage)
            ->first();

        return $grade ? $grade->grade : ($percentage >= 90 ? 'A+' : ($percentage >= 80 ? 'A' : ($percentage >= 70 ? 'B+' : ($percentage >= 60 ? 'B' : ($percentage >= 50 ? 'C' : ($percentage >= 40 ? 'D' : 'F'))))));
    }

    /**
     * Save all marks
     */
    public function saveMarks()
    {
        $this->validate([
            'selected_session_id' => 'required|exists:sessions,id',
            'selected_school_id' => 'required|exists:schools,id',
        ]);

        $savedCount = 0;

        foreach ($this->studentList as $student) {
            $studentcrId = $student['id'];
            $data = $this->marksData[$studentcrId] ?? null;
            
            if (!$data || $data['marks_obtained'] === null || $data['marks_obtained'] === '') {
                continue;
            }

            $marksObtained = (float) $data['marks_obtained'];
            $percentage = round(($marksObtained / $this->currentFullMark) * 100, 2);
            $grade = $this->getGrade($percentage);

            $dataToSave = [
                'studentcr_id' => $studentcrId,
                'myclass_id' => $this->currentMyclassId,
                'section_id' => $student['section_id'], // Get section_id from studentcr
                'semester_id' => $this->currentSemesterId,
                'subject_id' => $this->currentSubjectId,
                'exam_detail_id' => $this->currentExamDetailId,
                'exam_setting_id' => $this->currentSettingId,
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
                // Check if entry exists
                $existing = Ex30MarksEntry::where('studentcr_id', $studentcrId)
                    ->where('exam_setting_id', $this->currentSettingId)
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
        $this->closeModal();
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->isOpen = false;
        $this->isViewMode = false;
        $this->marksData = [];
        $this->studentList = [];
        $this->viewingStudentId = null;
        $this->viewingMarks = [];
    }

    /**
     * View marks for a specific student
     */
    public function viewStudentMarks($studentcrId)
    {
        $this->viewingStudentId = $studentcrId;
        $this->isViewMode = true;

        // Get all marks for this student in this exam
        $this->viewingMarks = Ex30MarksEntry::with(['subject', 'examSetting'])
            ->where('studentcr_id', $studentcrId)
            ->where('exam_detail_id', $this->currentExamDetailId)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->get();
    }

    /**
     * Toggle back to entry mode from view mode
     */
    public function backToEntryMode()
    {
        $this->isViewMode = false;
        $this->viewingStudentId = null;
        $this->viewingMarks = [];
    }

    /**
     * Delete marks entry
     */
    public function deleteMarksEntry($marksEntryId)
    {
        Ex30MarksEntry::find($marksEntryId)->delete();
        $this->loadStudentMarks();
        session()->flash('message', 'Marks entry deleted successfully.');
    }

    /**
     * Delete all marks for current setting
     */
    public function deleteAllMarks()
    {
        $deleted = Ex30MarksEntry::where('exam_setting_id', $this->currentSettingId)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->delete();

        $this->loadStudentMarks();
        session()->flash('message', "Deleted {$deleted} marks entries.");
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedSessionId()
    {
        $this->resetPage();
    }

    public function updatedSelectedSchoolId()
    {
        $this->resetPage();
    }
}
