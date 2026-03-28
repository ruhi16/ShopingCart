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

    // Modal state
    public $isOpen = false;
    public $editingSettingId = null;

    // Current exam detail being configured
    public $currentExamDetailId;
    public $currentMyclassSemesterId;
    public $currentExamDetailName;
    public $currentMyclassName;
    public $currentSemesterName;

    // Form data for settings
    public $subjectSettings = [];

    // Dropdown options
    public $sessionOptions = [];
    public $schoolOptions = [];
    
    // All options for reference
    public $examNames = [];
    public $examTypes = [];
    public $examParts = [];
    public $examModes = [];

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
            $examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
                ->where('myclass_id', $item->myclass_id)
                ->where('semester_id', $item->semester_id)
                ->where('session_id', $this->selected_session_id)
                ->where('school_id', $this->selected_school_id)
                ->where('is_active', 1)
                ->get();

            // Count settings for each exam detail and add as separate data
            $detailsWithCount = [];
            foreach ($examDetails as $examDetail) {
                // Get all settings for this exam detail with subject relationship
                $settings = Ex25Settings::with(['subject'])
                    ->where('exam_detail_id', $examDetail->id)
                    ->where('session_id', $this->selected_session_id)
                    ->where('school_id', $this->selected_school_id)
                    ->where('is_active', 1)
                    ->get();
                
                $detailsWithCount[] = [
                    'detail' => $examDetail,
                    'settings_count' => $settings->count(),
                    'settings' => $settings,
                ];
            }
            $item->examDetailsWithCount = $detailsWithCount;
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
     * Open modal to configure exam settings for a specific exam detail
     */
    public function configureExamSettings($myclassSemesterId, $examDetailId)
    {
        $myclassSemester = Bs09MyclassSemester::with(['myclass', 'semester'])
            ->findOrFail($myclassSemesterId);
        
        $examDetail = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
            ->findOrFail($examDetailId);

        $this->currentExamDetailId = $examDetailId;
        $this->currentMyclassSemesterId = $myclassSemesterId;
        $this->currentExamDetailName = implode(' - ', [
            $examDetail->examName->name ?? '',
            $examDetail->examType->name ?? '',
            $examDetail->examPart->name ?? '',
            '(' . ($examDetail->examMode->name ?? '') . ')'
        ]);
        $this->currentMyclassName = $myclassSemester->myclass->name ?? 'N/A';
        $this->currentSemesterName = $myclassSemester->semester->name ?? 'N/A';
        $this->editingSettingId = null;

        // Load subjects for this myclass
        $this->loadSubjectSettings();

        $this->isOpen = true;
    }

    /**
     * Load subject settings from database
     */
    private function loadSubjectSettings()
    {
        // Get subjects for this myclass
        $myclassSemester = Bs09MyclassSemester::find($this->currentMyclassSemesterId);
        $myclassId = $myclassSemester->myclass_id;

        $subjects = Bs07Subject::where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get();

        // Get existing settings
        $existingSettings = Ex25Settings::where('exam_detail_id', $this->currentExamDetailId)
            ->where('session_id', $this->selected_session_id)
            ->where('school_id', $this->selected_school_id)
            ->get()
            ->keyBy('subject_id');

        $this->subjectSettings = [];
        foreach ($subjects as $subject) {
            $existing = $existingSettings->get($subject->id);
            $this->subjectSettings[] = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'subject_code' => $subject->subject_code ?? '',
                'full_mark' => $existing ? $existing->full_mark : 100,
                'pass_mark' => $existing ? $existing->pass_mark : 33,
                'time_in_minutes' => $existing ? $existing->time_in_minutes : 60,
                'setting_id' => $existing ? $existing->id : null,
                'is_active' => $existing ? $existing->is_active : true,
            ];
        }
    }

    /**
     * Edit a specific setting
     */
    public function editSetting($settingId)
    {
        $setting = Ex25Settings::with('subject')->findOrFail($settingId);
        
        $this->editingSettingId = $settingId;
        $this->currentExamDetailId = $setting->exam_detail_id;
        $this->currentMyclassSemesterId = $setting->myclass_id; // Approximation

        $examDetail = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
            ->find($setting->exam_detail_id);

        $this->currentExamDetailName = $examDetail ? implode(' - ', [
            $examDetail->examName->name ?? '',
            $examDetail->examType->name ?? '',
            $examDetail->examPart->name ?? '',
            '(' . ($examDetail->examMode->name ?? '') . ')'
        ]) : 'N/A';

        $myclassSemester = Bs09MyclassSemester::with(['myclass', 'semester'])
            ->where('myclass_id', $setting->myclass_id)
            ->where('semester_id', $setting->semester_id)
            ->first();

        $this->currentMyclassName = $myclassSemester->myclass->name ?? 'N/A';
        $this->currentSemesterName = $myclassSemester->semester->name ?? 'N/A';

        // Load subject settings with only the specific subject
        $this->subjectSettings = [[
            'subject_id' => $setting->subject_id,
            'subject_name' => $setting->subject->name ?? 'N/A',
            'subject_code' => $setting->subject->subject_code ?? '',
            'full_mark' => $setting->full_mark,
            'pass_mark' => $setting->pass_mark,
            'time_in_minutes' => $setting->time_in_minutes,
            'setting_id' => $setting->id,
            'is_active' => $setting->is_active,
        ]];

        $this->isOpen = true;
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->isOpen = false;
        $this->editingSettingId = null;
        $this->currentExamDetailId = null;
        $this->currentMyclassSemesterId = null;
        $this->subjectSettings = [];
    }

    /**
     * Save all subject settings for the current exam detail
     */
    public function saveSettings()
    {
        $this->validate([
            'selected_session_id' => 'required|exists:sessions,id',
            'selected_school_id' => 'required|exists:schools,id',
        ]);

        $myclassSemester = Bs09MyclassSemester::find($this->currentMyclassSemesterId);
        $myclassId = $myclassSemester->myclass_id ?? null;
        $semesterId = $myclassSemester->semester_id ?? null;
        $sectionId = null;

        // Get exam detail for section
        $examDetail = Ex24Detail::find($this->currentExamDetailId);
        if ($examDetail && isset($examDetail->section_id)) {
            $sectionId = $examDetail->section_id;
        }

        $savedCount = 0;

        foreach ($this->subjectSettings as $setting) {
            if (!isset($setting['subject_id'])) {
                continue;
            }

            $fullMark = max(0, (int) ($setting['full_mark'] ?? 100));
            $passMark = min($fullMark, max(0, (int) ($setting['pass_mark'] ?? 33)));
            $timeMinutes = max(0, (int) ($setting['time_in_minutes'] ?? 60));

            $data = [
                'name' => 'Setting: ' . ($setting['subject_name'] ?? 'Unknown'),
                'myclass_id' => $myclassId,
                'section_id' => $sectionId,
                'semester_id' => $semesterId,
                'exam_detail_id' => $this->currentExamDetailId,
                'subject_id' => $setting['subject_id'],
                'full_mark' => $fullMark,
                'pass_mark' => $passMark,
                'time_in_minutes' => $timeMinutes,
                'session_id' => $this->selected_session_id,
                'school_id' => $this->selected_school_id,
                'is_active' => $setting['is_active'] ?? true,
            ];

            if (isset($setting['setting_id']) && $setting['setting_id']) {
                Ex25Settings::where('id', $setting['setting_id'])->update($data);
            } else {
                // Check if setting exists
                $existing = Ex25Settings::where('exam_detail_id', $this->currentExamDetailId)
                    ->where('subject_id', $setting['subject_id'])
                    ->where('session_id', $this->selected_session_id)
                    ->where('school_id', $this->selected_school_id)
                    ->first();

                if ($existing) {
                    $existing->update($data);
                } else {
                    Ex25Settings::create($data);
                }
            }

            $savedCount++;
        }

        session()->flash('message', "Successfully saved {$savedCount} settings.");
        $this->closeModal();
    }

    /**
     * Delete a specific setting
     */
    public function deleteSetting($settingId)
    {
        Ex25Settings::find($settingId)->delete();
        session()->flash('message', 'Setting deleted successfully.');
    }

    /**
     * Delete all settings for an exam detail
     */
    public function deleteAllSettingsForExamDetail($myclassSemesterId, $examDetailId)
    {
        $myclassSemester = Bs09MyclassSemester::find($myclassSemesterId);
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

    /**
     * Toggle setting status
     */
    public function toggleStatus($settingId)
    {
        $setting = Ex25Settings::find($settingId);
        $setting->update(['is_active' => !$setting->is_active]);
        session()->flash('message', 'Status updated successfully.');
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
