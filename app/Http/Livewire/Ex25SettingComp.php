<?php

namespace App\Http\Livewire;

use App\Models\Ex25Settings;
use App\Models\Bs09MyclassSemester;
use App\Models\Bs07Subject;
use App\Models\Ex24Detail;
use App\Models\Ex20Name;
use App\Models\Ex21Type;
use App\Models\Ex22Part;
use App\Models\Ex23Mode;
use App\Models\Session;
use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;

class Ex25SettingComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters
    public $selected_session_id;
    public $selected_school_id;
    public $search = '';
    public $perPage = 10;

    // Dropdown options
    public $sessionOptions = [];
    public $schoolOptions = [];
    
    // Exam detail options for dropdowns
    public $examNames = [];
    public $examTypes = [];
    public $examParts = [];
    public $examModes = [];

    // Form data - keyed by myclassSemesterId_examDetailId_subjectId
    public $formData = [];

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

        // Get class-semester combinations for the selected session and school
        $query = Bs09MyclassSemester::with(['myclass', 'semester'])
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id);

        if ($this->search) {
            $query->whereHas('myclass', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $myclassSemesters = $query->orderBy('myclass_id', 'asc')
            ->orderBy('semester_id', 'asc')
            ->paginate($this->perPage);

        // Load exam details and settings for each combination
        foreach ($myclassSemesters as $item) {
            // Load exam details for this myclass/semester
            $item->examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
                ->where('myclass_id', $item->myclass_id)
                ->where('semester_id', $item->semester_id)
                ->where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id)
                ->where('is_active', 1)
                ->get();

            // Load subjects for this myclass
            $item->subjects = Bs07Subject::where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id)
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->get();

            // Load existing settings keyed by myclassSemesterId_examDetailId_subjectId
            $item->settingsMap = Ex25Settings::where('myclass_id', $item->myclass_id)
                ->where('semester_id', $item->semester_id)
                ->where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id)
                ->get()
                ->keyBy(function($setting) use ($item) {
                    return $item->id . '_' . $setting->exam_detail_id . '_' . $setting->subject_id;
                });
        }

        return view('livewire.ex25-setting-comp', [
            'myclassSemesters' => $myclassSemesters,
        ]);
    }

    public function loadOptions()
    {
        $this->sessionOptions = Session::active()->orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
        $this->schoolOptions = School::active()->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        
        // Load exam detail components
        $this->examNames = Ex20Name::active()->orderBy('name', 'ASC')->get();
        $this->examTypes = Ex21Type::active()->orderBy('name', 'ASC')->get();
        $this->examParts = Ex22Part::all()->sortBy('name');
        $this->examModes = Ex23Mode::all()->sortBy('name');

        // Set defaults
        if (!$this->selected_session_id && !empty($this->sessionOptions)) {
            $this->selected_session_id = array_key_first($this->sessionOptions);
        }
        if (!$this->selected_school_id && !empty($this->schoolOptions)) {
            $this->selected_school_id = array_key_first($this->schoolOptions);
        }
    }

    /**
     * Get form data for a specific setting key
     */
    public function getFormDataValue($key, $field, $default = '')
    {
        return $this->formData[$key][$field] ?? $default;
    }

    /**
     * Set form data for a specific setting key
     */
    public function setFormData($key, $field, $value)
    {
        if (!isset($this->formData[$key])) {
            $this->formData[$key] = [];
        }
        $this->formData[$key][$field] = $value;
    }

    /**
     * Initialize form data for a specific setting
     */
    public function initFormData($myclassSemesterId, $examDetailId, $subjectId, $fullMark = 100, $passMark = 33, $timeMinutes = 60)
    {
        $key = $myclassSemesterId . '_' . $examDetailId . '_' . $subjectId;
        if (!isset($this->formData[$key])) {
            $this->formData[$key] = [
                'full_mark' => $fullMark,
                'pass_mark' => $passMark,
                'time_in_minutes' => $timeMinutes,
            ];
        }
    }

    /**
     * Save settings for a specific exam detail and all its subjects
     */
    public function saveExamSettings($myclassSemesterId, $examDetailId)
    {
        $myclassSemester = Bs09MyclassSemester::findOrFail($myclassSemesterId);
        $myclassId = $myclassSemester->myclass_id;
        $semesterId = $myclassSemester->semester_id;
        $sectionId = null;

        // Get exam detail to check for section
        $examDetail = Ex24Detail::find($examDetailId);
        if ($examDetail && isset($examDetail->section_id)) {
            $sectionId = $examDetail->section_id;
        }

        // Get subjects for this myclass
        $subjects = Bs07Subject::where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->get();

        $savedCount = 0;

        foreach ($subjects as $subject) {
            $key = $myclassSemesterId . '_' . $examDetailId . '_' . $subject->id;
            $data = $this->formData[$key] ?? null;

            if (!$data) {
                continue;
            }

            $fullMark = max(0, (int) ($data['full_mark'] ?? 100));
            $passMark = min($fullMark, max(0, (int) ($data['pass_mark'] ?? 33)));
            $timeMinutes = max(0, (int) ($data['time_in_minutes'] ?? 60));

            // Find existing or create new
            $existing = Ex25Settings::where('myclass_id', $myclassId)
                ->where('semester_id', $semesterId)
                ->where('exam_detail_id', $examDetailId)
                ->where('subject_id', $subject->id)
                ->where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id)
                ->first();

            $settingData = [
                'name' => 'Setting: ' . $subject->name,
                'myclass_id' => $myclassId,
                'section_id' => $sectionId,
                'semester_id' => $semesterId,
                'exam_detail_id' => $examDetailId,
                'subject_id' => $subject->id,
                'full_mark' => $fullMark,
                'pass_mark' => $passMark,
                'time_in_minutes' => $timeMinutes,
                'session_id' => $this->selected_session_id,
                'school_id' => $this->selected_school_id,
                'is_active' => 1,
            ];

            if ($existing) {
                $existing->update($settingData);
            } else {
                Ex25Settings::create($settingData);
            }

            $savedCount++;
        }

        session()->flash('message', "Successfully saved {$savedCount} settings.");
    }

    /**
     * Delete all settings for a specific exam detail
     */
    public function deleteExamSettings($myclassSemesterId, $examDetailId)
    {
        $myclassSemester = Bs09MyclassSemester::findOrFail($myclassSemesterId);
        $myclassId = $myclassSemester->myclass_id;
        $semesterId = $myclassSemester->semester_id;

        $deleted = Ex25Settings::where('myclass_id', $myclassId)
            ->where('semester_id', $semesterId)
            ->where('exam_detail_id', $examDetailId)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->delete();

        session()->flash('message', "Deleted {$deleted} settings.");
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedSessionId()
    {
        $this->formData = []; // Reset form data when filters change
    }

    public function updatedSelectedSchoolId()
    {
        $this->formData = []; // Reset form data when filters change
    }
}
