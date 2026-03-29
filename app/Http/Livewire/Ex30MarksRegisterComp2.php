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
    public $selected_myclass_id;
    public $search = '';
    public $perPage = 10;

    // Auto-selected values
    public $selected_session_id;
    public $selected_school_id;

    // Options
    public $myclassOptions = [];

    // Data
    public $students;
    public $subjects = [];
    public $examDetails = [];
    public $marksData = [];
    public $studentSubjectCounts = [];

    protected function rules()
    {
        return [
            'selected_myclass_id' => 'required|exists:bs04_myclasses,id',
        ];
    }

    public function mount()
    {
        // Auto-select active session and user's school
        $activeSession = Session::active()->first();
        $this->selected_session_id = $activeSession ? $activeSession->id : null;
        $this->selected_school_id = auth()->user()->school_id;

        $this->loadOptions();
    }

    public function render()
    {
        $this->loadOptions();

        // Only load data if class is selected
        if ($this->selected_myclass_id) {
            $this->loadRegisterData();
        }

        return view('livewire.ex30-marks-register-comp2');
    }

    private function loadRegisterData()
    {
        $this->students = collect();
        $this->subjects = [];
        $this->examDetails = [];
        $this->marksData = [];

        if (!$this->selected_session_id || !$this->selected_school_id || !$this->selected_myclass_id) {
            return;
        }

        // 1. Get students for selected myclass
        $query = Bs11Studentcr::with(['studentdb', 'currentMyclass', 'currentSection', 'currentSemester'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('current_myclass_id', $this->selected_myclass_id)
            ->where('is_active', 1);

        if ($this->search) {
            $query->whereHas('studentdb', function ($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%');
            });
        }

        $this->students = $query->orderBy('roll_no', 'asc')->get();

        if ($this->students->isEmpty()) return;

        // 2. Find every exam_detail_ids for all semesters allowed for that class
        $allowedSemesters = Bs09MyclassSemester::where('myclass_id', $this->selected_myclass_id)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->pluck('semester_id');

        // Then get all exam settings for these semesters and class
        $examSettings = Ex25Settings::with(['examDetail', 'subject'])
            ->where('myclass_id', $this->selected_myclass_id)
            ->whereIn('semester_id', $allowedSemesters)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->get();

        $examSettingsByDetail = $examSettings->groupBy('exam_detail_id');
        $examDetailIds = $examSettingsByDetail->keys();

        $this->examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
            ->whereIn('id', $examDetailIds)
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get();

        // 3. Get students' allotted subjects
        $studentdbIds = $this->students->pluck('studentdb_id')->unique();
        $studentSubjects = Bs12StudentdbSubject::with(['subject'])
            ->whereIn('studentdb_id', $studentdbIds)
            ->where('is_active', 1)
            ->get()
            ->groupBy('studentdb_id');

        // 4. Get all marks entries for these students
        $studentcrIds = $this->students->pluck('id');
        $allMarks = Ex30MarksEntry::whereIn('studentcr_id', $studentcrIds)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->get()
            ->groupBy('studentcr_id');

        // 5. Build marksData organized by Student -> Subject -> Exam
        foreach ($this->students as $student) {
            $studentcrId = $student->id;
            $studentdbId = $student->studentdb_id;

            $subjectsOpted = $studentSubjects->get($studentdbId, collect());
            $this->marksData[$studentcrId] = [];

            foreach ($subjectsOpted as $studentSubject) {
                $subjectId = $studentSubject->subject_id;
                $this->marksData[$studentcrId][$subjectId] = [
                    'subject_info' => $studentSubject,
                    'exam_marks' => [],
                ];

                foreach ($this->examDetails as $examDetail) {
                    $examDetailId = $examDetail->id;
                    $setting = $examSettingsByDetail->get($examDetailId, collect())
                        ->firstWhere('subject_id', $subjectId);

                    $marksEntry = $allMarks->get($studentcrId, collect())
                        ->where('exam_detail_id', $examDetailId)
                        ->where('subject_id', $subjectId)
                        ->first();

                    $fullMark = $setting ? $setting->full_mark : 0;
                    $marksObtained = $marksEntry ? $marksEntry->marks_obtained : null;
                    $isAbsent = $marksEntry ? ($marksEntry->is_absent ?? ($marksObtained == -99)) : false;
                    $percentage = ($marksEntry && $fullMark > 0) ? round(($marksObtained / $fullMark) * 100, 2) : 0;
                    $grade = $marksEntry ? $marksEntry->marks_grade : 'N/A';

                    if ($marksObtained !== null && !$isAbsent && $grade === 'N/A') {
                        $grade = $this->calculateGrade($percentage);
                    }

                    $this->marksData[$studentcrId][$subjectId]['exam_marks'][$examDetailId] = [
                        'exam_detail_id' => $examDetailId,
                        'exam_setting_id' => $setting ? $setting->id : null,
                        'full_mark' => $fullMark,
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
        if ($percentage >= 80) return 'A+';
        if ($percentage >= 70) return 'A';
        if ($percentage >= 60) return 'A-';
        if ($percentage >= 50) return 'B';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 33) return 'D';
        return 'F';
    }

    public function loadOptions()
    {
        if ($this->selected_school_id) {
            $this->myclassOptions = Bs04Myclass::where('school_id', $this->selected_school_id)
                ->where('is_active', 1)
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    public function updatedSelectedMyclassId()
    {
        $this->students = collect();
        $this->marksData = [];
    }

    public function updatingSearch()
    {
        // No pagination needed
    }

    public function resetFilters()
    {
        $this->selected_myclass_id = null;
        $this->search = '';
        $this->students = null;
        $this->marksData = [];
    }
}
