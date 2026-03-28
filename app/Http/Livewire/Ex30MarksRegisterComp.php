<?php

namespace App\Http\Livewire;

use App\Models\Ex30MarksEntry;
use App\Models\Ex25Settings;
use App\Models\Bs11Studentcr;
use App\Models\Bs09MyclassSemester;
use App\Models\Session;
use App\Models\School;
use App\Models\Bs04Myclass;
use App\Models\Bs05Semester;
use Livewire\Component;
use Livewire\WithPagination;

class Ex30MarksRegisterComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters
    public $selected_session_id;
    public $selected_school_id;
    public $selected_myclass_id;
    public $search = '';
    public $perPage = 10;
    public $viewMode = 'compact'; // 'compact' or 'tabular'

    // Options
    public $sessionOptions = [];
    public $myclassOptions = [];

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

        // Get all semesters for selected myclass
        $semesters = Bs05Semester::orderBy('id', 'asc')->get();

        // Get studentcrs grouped by myclass and semester
        $query = Bs11Studentcr::with(['studentdb'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1);

        if ($this->selected_myclass_id) {
            $query->where('current_myclass_id', $this->selected_myclass_id);
        }

        if ($this->search) {
            $query->whereHas('studentdb', function($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%');
            });
        }

        $studentcrs = $query->orderBy('current_myclass_id', 'asc')
            ->orderBy('current_sememster_id', 'asc')
            ->orderBy('roll_no', 'asc')
            ->paginate($this->perPage);

        // Group studentcrs by myclass and semester
        $groupedData = [];
        foreach ($studentcrs as $studentcr) {
            $myclassId = $studentcr->current_myclass_id;
            $semesterId = $studentcr->current_sememster_id;
            
            $key = $myclassId . '-' . $semesterId;
            
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'myclass_id' => $myclassId,
                    'semester_id' => $semesterId,
                    'students' => [],
                ];
            }
            
            $groupedData[$key]['students'][] = $studentcr;
        }

        // Load marks for each student
        $this->loadStudentMarks($groupedData);

        return view('livewire.ex30-marks-register-comp', [
            'studentcrs' => $studentcrs,
            'groupedData' => $groupedData,
            'semesters' => $semesters,
        ]);
    }

    private function loadStudentMarks(&$groupedData)
    {
        // First pass: collect all marks for all students
        $allStudentIds = [];
        foreach ($groupedData as $data) {
            foreach ($data['students'] as $studentcr) {
                $allStudentIds[] = $studentcr->id;
            }
        }

        // Bulk load marks for all students
        $allMarks = Ex30MarksEntry::whereIn('studentcr_id', $allStudentIds)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->with(['examSetting.subject'])
            ->get()
            ->groupBy('studentcr_id');

        // Process each student's marks
        foreach ($groupedData as $key => &$data) {
            $myclassId = $data['myclass_id'];
            $semesterId = $data['semester_id'];
            
            // Get all exam settings for this myclass/semester
            $examSettings = Ex25Settings::with(['subject', 'examDetail'])
                ->where('myclass_id', $myclassId)
                ->where('semester_id', $semesterId)
                ->where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id)
                ->where('is_active', 1)
                ->get()
                ->keyBy('id');

            foreach ($data['students'] as &$studentcr) {
                $studentMarks = $allMarks->get($studentcr->id, collect());
                $marksData = [];
                $totalObtained = 0;
                $totalFullMark = 0;
                $subjectsPassed = 0;
                $totalSubjects = 0;

                foreach ($examSettings as $settingId => $setting) {
                    $marksEntry = $studentMarks->firstWhere('exam_setting_id', $settingId);

                    $marksData[$settingId] = [
                        'subject_id' => $setting->subject_id,
                        'subject_name' => $setting->subject->name ?? 'N/A',
                        'subject_code' => $setting->subject->subject_code ?? '',
                        'full_mark' => $setting->full_mark,
                        'pass_mark' => $setting->pass_mark,
                        'marks_obtained' => $marksEntry ? $marksEntry->marks_obtained : null,
                        'percentage' => $marksEntry ? $marksEntry->marks_percentage : null,
                        'grade' => $marksEntry ? $marksEntry->marks_grade : null,
                    ];

                    if ($marksEntry && $marksEntry->marks_obtained !== null) {
                        $totalObtained += $marksEntry->marks_obtained;
                        $totalFullMark += $setting->full_mark;
                        $totalSubjects++;
                        
                        if ($marksEntry->marks_obtained >= $setting->pass_mark) {
                            $subjectsPassed++;
                        }
                    } else {
                        $totalFullMark += $setting->full_mark;
                        $totalSubjects++;
                    }
                }

                // Store marks data in a separate array keyed by studentcr_id
                $data['marks_by_student'][$studentcr->id] = [
                    'marksData' => $marksData,
                    'totalObtained' => $totalObtained,
                    'totalFullMark' => $totalFullMark,
                    'overallPercentage' => $totalFullMark > 0 ? round(($totalObtained / $totalFullMark) * 100, 2) : 0,
                    'overallGrade' => $this->getOverallGrade($totalFullMark > 0 ? round(($totalObtained / $totalFullMark) * 100, 2) : 0),
                    'subjectsPassed' => $subjectsPassed,
                    'totalSubjects' => $totalSubjects,
                ];
            }
        }
    }

    private function getOverallGrade($percentage)
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
        $this->sessionOptions = Session::active()->orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
        $this->myclassOptions = Bs04Myclass::where('session_id', $this->selected_session_id ?? 0)
            ->where('school_id', $this->selected_school_id ?? 0)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        // Set defaults
        if (!$this->selected_session_id && !empty($this->sessionOptions)) {
            $this->selected_session_id = array_key_first($this->sessionOptions);
        }
        if (!$this->selected_school_id && !empty($this->sessionOptions)) {
            $this->selected_school_id = auth()->user()->school_id ?? 1;
        }
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedSessionId()
    {
        $this->resetPage();
        $this->selected_myclass_id = null;
    }

    public function updatedSelectedSchoolId()
    {
        $this->resetPage();
        $this->selected_myclass_id = null;
    }
}
