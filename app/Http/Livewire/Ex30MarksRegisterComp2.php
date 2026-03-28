<?php

namespace App\Http\Livewire;

use App\Models\Ex30MarksEntry;
use App\Models\Ex25Settings;
use App\Models\Bs11Studentcr;
use App\Models\Bs12StudentdbSubject;
use App\Models\Bs09MyclassSemester;
use App\Models\Session;
use App\Models\School;
use App\Models\Bs04Myclass;
use App\Models\Bs05Semester;
use App\Models\Bs07Subject;
use App\Models\Ex24Detail;
use Livewire\Component;

class Ex30MarksRegisterComp2 extends Component
{
    // Filters
    public $selected_session_id;
    public $selected_school_id;
    public $selected_myclass_id;
    public $selected_semester_id;
    public $search = '';
    public $perPage = 10;

    // Options
    public $sessionOptions = [];
    public $schoolOptions = [];
    public $myclassOptions = [];
    public $semesterOptions = [];

    // Data
    public $students;
    public $subjects = [];
    public $examDetails = [];
    public $marksData = [];
    public $studentSubjectCounts = [];

    protected function rules()
    {
        return [
            'selected_session_id' => 'required|exists:sessions,id',
            'selected_school_id' => 'required|exists:schools,id',
            'selected_myclass_id' => 'required|exists:bs04_myclasses,id',
            'selected_semester_id' => 'required|exists:bs05_semesters,id',
        ];
    }

    public function mount()
    {
        $this->loadOptions();
    }

    public function render()
    {
        $this->loadOptions();
        $this->loadRegisterData();

        return view('livewire.ex30-marks-register-comp2');
    }

    private function loadRegisterData()
    {
        $this->students = collect();
        $this->subjects = [];
        $this->examDetails = [];
        $this->marksData = [];
        $this->studentSubjectCounts = [];

        // Validate required selections
        if (!$this->selected_session_id || !$this->selected_school_id || 
            !$this->selected_myclass_id || !$this->selected_semester_id) {
            return;
        }

        // Step 1: Get all studentcrs for selected session, school, and myclass
        $query = Bs11Studentcr::with(['studentdb', 'currentMyclass', 'currentSection', 'currentSemester'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('current_myclass_id', $this->selected_myclass_id)
            ->where('current_semester_id', $this->selected_semester_id)
            ->where('is_active', 1);

        if ($this->search) {
            $query->whereHas('studentdb', function($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%');
            });
        }

        $this->students = $query->orderBy('roll_no', 'asc')->get();

        if ($this->students->isEmpty()) {
            return;
        }

        // Step 2: Get all subjects opted by each student (from bs12_studentdb_subjects)
        $studentdbIds = $this->students->pluck('studentdb_id')->unique()->toArray();
        
        $studentSubjects = Bs12StudentdbSubject::with(['subject'])
            ->whereIn('studentdb_id', $studentdbIds)
            ->where('is_active', 1)
            ->get()
            ->groupBy('studentdb_id');

        // Collect all unique subjects
        $allSubjectIds = [];
        foreach ($studentSubjects as $studentdbId => $subjects) {
            foreach ($subjects as $studentSubject) {
                $allSubjectIds[] = $studentSubject->subject_id;
            }
            // Store subject count for each student
            $this->studentSubjectCounts[$studentdbId] = $subjects->count();
        }
        $allSubjectIds = array_unique($allSubjectIds);

        // Get subject details - use Bs12StudentdbSubject to get subject_id and subject relationship
        $this->subjects = Bs12StudentdbSubject::with(['subject'])
            ->whereIn('subject_id', $allSubjectIds)
            ->where('is_active', 1)
            ->get()
            ->unique('subject_id');

        // Step 3: Get all exam details for selected myclass and semester
        $examSettings = Ex25Settings::with(['examDetail', 'subject'])
            ->where('myclass_id', $this->selected_myclass_id)
            ->where('semester_id', $this->selected_semester_id)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->get();

        // Group exam settings by exam_detail_id
        $examSettingsByDetail = $examSettings->groupBy('exam_detail_id');

        // Get exam details
        $examDetailIds = $examSettingsByDetail->keys()->toArray();
        $this->examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
            ->whereIn('id', $examDetailIds)
            ->where('is_active', 1)
            ->get();

        // Step 4: Get all marks entries for these students
        $studentcrIds = $this->students->pluck('id')->toArray();
        
        $allMarks = Ex30MarksEntry::whereIn('studentcr_id', $studentcrIds)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->get()
            ->groupBy('studentcr_id');

        // Step 5: Build marks data for each student organized by SUBJECT (not by exam)
        foreach ($this->students as $student) {
            $studentcrId = $student->id;
            $studentdbId = $student->studentdb_id;
            
            // Get subjects opted by this student
            $studentSubjectList = $studentSubjects->get($studentdbId, collect());
            $studentSubjectIds = $studentSubjectList->pluck('subject_id')->toArray();

            // Initialize marks data for this student - organized by SUBJECT first
            $this->marksData[$studentcrId] = [];

            // For each SUBJECT opted by the student
            foreach ($studentSubjectIds as $subjectId) {
                // Initialize subject marks
                $this->marksData[$studentcrId][$subjectId] = [
                    'subject_info' => $studentSubjectList->firstWhere('subject_id', $subjectId),
                    'exam_marks' => [],
                ];

                // For each exam detail, collect marks for this subject
                foreach ($this->examDetails as $examDetail) {
                    $examDetailId = $examDetail->id;

                    // Get exam settings for this exam detail and subject
                    $settingsForDetail = $examSettingsByDetail->get($examDetailId, collect());
                    $setting = $settingsForDetail->firstWhere('subject_id', $subjectId);
                    
                    // Get marks entry for this student, subject, and exam detail
                    $studentMarks = $allMarks->get($studentcrId, collect());
                    $marksEntry = $studentMarks->first(function($mark) use ($examDetailId, $subjectId) {
                        return $mark->exam_detail_id == $examDetailId && $mark->subject_id == $subjectId;
                    });

                    $fullMark = $setting ? $setting->full_mark : 0;
                    $passMark = $setting ? $setting->pass_mark : 0;
                    $examSettingId = $setting ? $setting->id : null;

                    $marksObtained = $marksEntry ? $marksEntry->marks_obtained : null;
                    $isAbsent = $marksEntry ? ($marksEntry->is_absent ?? ($marksObtained == -99)) : false;
                    $grade = $marksEntry ? $marksEntry->marks_grade : 'N/A';
                    $percentage = $marksEntry ? $marksEntry->marks_percentage : 0;

                    // Calculate grade if marks exist but grade is not set
                    if ($marksObtained !== null && !$isAbsent && $grade === 'N/A') {
                        $percentage = $fullMark > 0 ? round(($marksObtained / $fullMark) * 100, 2) : 0;
                        $grade = $this->calculateGrade($percentage);
                    }

                    // Store marks for this exam
                    $this->marksData[$studentcrId][$subjectId]['exam_marks'][$examDetailId] = [
                        'exam_detail_id' => $examDetailId,
                        'exam_setting_id' => $examSettingId,
                        'full_mark' => $fullMark,
                        'pass_mark' => $passMark,
                        'marks_obtained' => $marksObtained,
                        'is_absent' => $isAbsent,
                        'grade' => $grade,
                        'percentage' => $percentage,
                    ];
                }
            }
        }
    }

    public function calculateStudentTotal($studentcrId)
    {
        $totalObtained = 0;
        $totalFull = 0;

        if (!isset($this->marksData[$studentcrId])) {
            return [
                'total_obtained' => 0,
                'total_full' => 0,
                'percentage' => 0,
                'grade' => 'N/A',
            ];
        }

        foreach ($this->marksData[$studentcrId] as $subjectMarks) {
            foreach ($subjectMarks as $examDetailId => $markData) {
                if ($markData['marks_obtained'] !== null && !$markData['is_absent']) {
                    $totalObtained += $markData['marks_obtained'];
                    $totalFull += $markData['full_mark'];
                } elseif ($markData['full_mark'] > 0) {
                    $totalFull += $markData['full_mark'];
                }
            }
        }

        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $grade = $this->calculateGrade($percentage);

        return [
            'total_obtained' => $totalObtained,
            'total_full' => $totalFull,
            'percentage' => $percentage,
            'grade' => $grade,
        ];
    }

    public function calculateSubjectTotal($subjectId)
    {
        $totalObtained = 0;
        $totalFull = 0;
        $count = 0;

        foreach ($this->students as $student) {
            $studentcrId = $student->id;
            
            if (!isset($this->marksData[$studentcrId][$subjectId])) {
                continue;
            }

            foreach ($this->marksData[$studentcrId][$subjectId] as $examDetailId => $markData) {
                if ($markData['marks_obtained'] !== null && !$markData['is_absent']) {
                    $totalObtained += $markData['marks_obtained'];
                    $totalFull += $markData['full_mark'];
                    $count++;
                } elseif ($markData['full_mark'] > 0) {
                    $totalFull += $markData['full_mark'];
                    $count++;
                }
            }
        }

        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $grade = $this->calculateGrade($percentage);

        return [
            'total_obtained' => $totalObtained,
            'total_full' => $totalFull,
            'percentage' => $percentage,
            'grade' => $grade,
            'count' => $count,
        ];
    }

    public function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
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

        // MyClass options (based on session)
        if ($this->selected_session_id) {
            $myclassIds = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id ?? 0)
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

        // Semester options (based on selected myclass)
        if ($this->selected_session_id && $this->selected_myclass_id) {
            $semesterIds = Bs11Studentcr::where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id ?? 0)
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

        // Set defaults
        if (!$this->selected_session_id && !empty($this->sessionOptions)) {
            $this->selected_session_id = array_key_first($this->sessionOptions);
        }
        if (!$this->selected_school_id) {
            $this->selected_school_id = auth()->user()->school_id ?? 1;
        }
    }

    public function updatedSelectedSessionId()
    {
        $this->selected_myclass_id = null;
        $this->selected_semester_id = null;
        $this->students = collect();
        $this->marksData = [];
    }

    public function updatedSelectedSchoolId()
    {
        $this->selected_myclass_id = null;
        $this->selected_semester_id = null;
        $this->students = collect();
        $this->marksData = [];
    }

    public function updatedSelectedMyclassId()
    {
        $this->selected_semester_id = null;
        $this->students = collect();
        $this->marksData = [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->selected_session_id = null;
        $this->selected_school_id = null;
        $this->selected_myclass_id = null;
        $this->selected_semester_id = null;
        $this->search = '';
        $this->students = collect();
        $this->marksData = [];
        $this->loadOptions();
    }
}
