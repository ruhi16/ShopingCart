<?php

namespace App\Http\Livewire;

use App\Models\Bs04Myclass;
use App\Models\Bs07Subject;
use App\Models\Bs11Studentcr;
use App\Models\Bs05Semester;
use App\Models\Ex24Detail;
use App\Models\Ex25Settings;
use App\Models\Ex30MarksEntry;
use App\Models\Session;
use App\Models\School;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Ex30MarksEntryCombSemestersComp extends Component
{
    public $selectedClassId = null;
    public $selectedSubjectId = null;
    public $sessionId = null;
    public $schoolId = null;
    
    public $myclasses = [];
    public $subjects = [];
    
    public $semesters = [];
    public $students = [];
    public $examDetailsBySemester = [];
    public $examSettingsBySemester = [];
    
    public $marksData = [];
    
    protected $rules = [
        'selectedClassId' => 'required|exists:bs04_myclasses,id',
        'selectedSubjectId' => 'required|exists:bs07_subjects,id',
    ];

    public function mount()
    {
        // Ensure authentication is available
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        // Get authenticated user's school_id
        $user = Auth::user();
        $this->schoolId = $user ? (int)$user->school_id : null;
        
        // Get currently active session
        $activeSession = Session::active()->first();
        $this->sessionId = $activeSession ? (int)$activeSession->id : null;
        
        // Load classes and subjects with auto-selected session and school
        $this->loadClasses();
        $this->loadSubjects();
    }

    public function loadClasses()
    {
        if ($this->sessionId && $this->schoolId) {
            $this->myclasses = Bs04Myclass::where('session_id', $this->sessionId)
                ->where('school_id', $this->schoolId)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        } else {
            $this->myclasses = [];
        }
    }

    public function loadSubjects()
    {
        if ($this->sessionId && $this->schoolId) {
            $this->subjects = Bs07Subject::where('session_id', $this->sessionId)
                ->where('school_id', $this->schoolId)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        } else {
            $this->subjects = [];
        }
    }

    public function updatedSelectedClassId()
    {
        // Only reset subject selection and marks data, keep session and school
        $this->selectedSubjectId = null;
        $this->subjects = Bs07Subject::where('session_id', $this->sessionId)
            ->where('school_id', $this->schoolId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        $this->semesters = [];
        $this->examDetailsBySemester = [];
        $this->examSettingsBySemester = [];
        $this->students = [];
        $this->marksData = [];
    }

    public function updatedSelectedSubjectId()
    {
        if ($this->selectedClassId && $this->selectedSubjectId) {
            // Load semesters and exam details first (before students)
            $this->loadSemestersAndExamDetails();
            // Then load students (filtered by subject) and initialize marks data
            $this->loadStudents();
        }
    }

    public function loadStudents()
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId) {
            return;
        }

        // Get students for selected class who are enrolled in the selected subject
        $this->students = Bs11Studentcr::with(['studentdb', 'studentdb.subjects'])
            ->where('current_myclass_id', $this->selectedClassId)
            ->where('session_id', $this->sessionId)
            ->where('is_active', 1)
            ->get()
            ->filter(function($student) {
                // Filter students who are enrolled in the selected subject
                if ($student->studentdb && $student->studentdb->subjects) {
                    return $student->studentdb->subjects->contains('id', $this->selectedSubjectId);
                }
                return false;
            })
            ->map(function($student) {
                // Join with studentdb to get board_reg_no
                $student->board_reg_no = $student->studentdb->board_reg_no ?? '';
                return $student;
            })
            ->sortBy('board_reg_no')
            ->values()
            ->values();

        // Initialize marksData for these students
        $this->initializeMarksData();
    }
    
    public function initializeMarksData()
    {
        $this->marksData = [];
        
        foreach ($this->students as $student) {
            foreach ($this->semesters as $semester) {
                $examDetails = $this->examDetailsBySemester[$semester->id] ?? [];
                
                foreach ($examDetails as $detail) {
                    // Check if exam setting exists
                    $setting = $this->examSettingsBySemester[$semester->id][$detail->id] ?? null;
                    
                    // Check for existing marks entry
                    $existingMark = Ex30MarksEntry::where('studentcr_id', $student->id)
                        ->where('semester_id', $semester->id)
                        ->where('exam_detail_id', $detail->id)
                        ->where('subject_id', $this->selectedSubjectId)
                        ->where('myclass_id', $this->selectedClassId)
                        ->where('session_id', $this->sessionId)
                        ->first();
                    
                    $this->marksData[$student->id][$semester->id][$detail->id] = [
                        'marks_obtained' => $existingMark ? $existingMark->marks_obtained : null,
                        'full_mark' => $setting ? $setting->full_mark : null,
                        'exam_setting_id' => $setting ? $setting->id : null,
                        'grade' => $existingMark ? $existingMark->marks_grade : null,
                        'remarks' => $existingMark ? $existingMark->remarks : null,
                        'is_absent' => $existingMark ? $existingMark->is_absent : false,
                    ];
                }
            }
        }
    }

    public function loadSemestersAndExamDetails()
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId) {
            return;
        }

        // Get all semesters for the selected class using the myclass_semesters pivot table
        $selectedClassId = $this->selectedClassId;
        $this->semesters = Bs05Semester::whereHas('admissionSemester', function($query) use ($selectedClassId) {
            $query->where('current_myclass_id', $selectedClassId);
        })
        ->orWhereHas('semesterClasses', function($query) use ($selectedClassId) {
            $query->where('bs09_myclass_semesters.myclass_id', $selectedClassId);
        })
        ->distinct()
        ->get();

        // If no semesters found through relationships, get all active semesters
        if ($this->semesters->isEmpty()) {
            $this->semesters = Bs05Semester::active()->get();
        }

        // For each semester, get exam details and settings
        foreach ($this->semesters as $semester) {
            $semesterId = $semester->id;

            // Get exam details for this class, semester, and session
            $examDetails = Ex24Detail::where('myclass_id', $this->selectedClassId)
                ->where('semester_id', $semesterId)
                ->where('session_id', $this->sessionId)
                ->get();

            $this->examDetailsBySemester[$semesterId] = $examDetails;

            // Get exam settings for each exam detail
            $settings = [];
            foreach ($examDetails as $detail) {
                $setting = Ex25Settings::where('exam_detail_id', $detail->id)
                    ->where('myclass_id', $this->selectedClassId)
                    ->where('semester_id', $semesterId)
                    ->where('subject_id', $this->selectedSubjectId)
                    ->where('session_id', $this->sessionId)
                    ->first();

                if ($setting) {
                    $settings[$detail->id] = $setting;
                }
            }

            $this->examSettingsBySemester[$semesterId] = $settings;
        }
    }

    public function saveMarks()
    {
        $this->validate();

        $savedCount = 0;
        $updatedCount = 0;

        foreach ($this->marksData as $studentcrId => $semesterData) {
            foreach ($semesterData as $semesterId => $examData) {
                foreach ($examData as $examDetailId => $markData) {
                    if (isset($markData['marks_obtained'])) {
                        // Check if record already exists
                        $existingRecord = Ex30MarksEntry::where('studentcr_id', $studentcrId)
                            ->where('myclass_id', $this->selectedClassId)
                            ->where('semester_id', $semesterId)
                            ->where('subject_id', $this->selectedSubjectId)
                            ->where('exam_detail_id', $examDetailId)
                            ->where('session_id', $this->sessionId)
                            ->first();

                        if ($existingRecord) {
                            // Update existing record
                            $existingRecord->update([
                                'marks_obtained' => $markData['marks_obtained'] ?? null,
                                'marks_percentage' => $this->calculatePercentage(
                                    $markData['marks_obtained'] ?? null,
                                    $markData['full_mark'] ?? null
                                ),
                                'marks_grade' => $markData['grade'] ?? null,
                                'remarks' => $markData['remarks'] ?? null,
                            ]);
                            $updatedCount++;
                        } else {
                            // Create new record
                            Ex30MarksEntry::create([
                                'studentcr_id' => $studentcrId,
                                'myclass_id' => $this->selectedClassId,
                                'section_id' => $this->getCurrentSection($studentcrId),
                                'semester_id' => $semesterId,
                                'subject_id' => $this->selectedSubjectId,
                                'exam_detail_id' => $examDetailId,
                                'exam_setting_id' => $markData['exam_setting_id'] ?? null,
                                'marks_obtained' => $markData['marks_obtained'] ?? null,
                                'marks_percentage' => $this->calculatePercentage(
                                    $markData['marks_obtained'] ?? null,
                                    $markData['full_mark'] ?? null
                                ),
                                'marks_grade' => $markData['grade'] ?? null,
                                'is_absent' => $markData['is_absent'] ?? false,
                                'session_id' => $this->sessionId,
                                'school_id' => $this->schoolId,
                                'is_active' => true,
                                'remarks' => $markData['remarks'] ?? null,
                            ]);
                            $savedCount++;
                        }
                    }
                }
            }
        }

        session()->flash('message', "Successfully saved {$savedCount} new records and updated {$updatedCount} existing records.");
        $this->dispatchBrowserEvent('show-message', ['message' => "Marks saved successfully!"]);
    }

    public function saveStudentMarks($studentId)
    {
        $savedCount = 0;
        $updatedCount = 0;

        if (!isset($this->marksData[$studentId])) {
            session()->flash('error', 'No marks data found for this student.');
            return;
        }

        $studentMarks = $this->marksData[$studentId];

        foreach ($studentMarks as $semesterId => $examData) {
            foreach ($examData as $examDetailId => $markData) {
                if (isset($markData['marks_obtained'])) {
                    // Check if record already exists
                    $existingRecord = Ex30MarksEntry::where('studentcr_id', $studentId)
                        ->where('myclass_id', $this->selectedClassId)
                        ->where('semester_id', $semesterId)
                        ->where('subject_id', $this->selectedSubjectId)
                        ->where('exam_detail_id', $examDetailId)
                        ->where('session_id', $this->sessionId)
                        ->first();

                    if ($existingRecord) {
                        // Update existing record
                        $existingRecord->update([
                            'marks_obtained' => $markData['marks_obtained'] ?? null,
                            'marks_percentage' => $this->calculatePercentage(
                                $markData['marks_obtained'] ?? null,
                                $markData['full_mark'] ?? null
                            ),
                            'marks_grade' => $markData['grade'] ?? null,
                            'remarks' => $markData['remarks'] ?? null,
                        ]);
                        $updatedCount++;
                    } else {
                        // Create new record
                        Ex30MarksEntry::create([
                            'studentcr_id' => $studentId,
                            'myclass_id' => $this->selectedClassId,
                            'section_id' => $this->getCurrentSection($studentId),
                            'semester_id' => $semesterId,
                            'subject_id' => $this->selectedSubjectId,
                            'exam_detail_id' => $examDetailId,
                            'exam_setting_id' => $markData['exam_setting_id'] ?? null,
                            'marks_obtained' => $markData['marks_obtained'] ?? null,
                            'marks_percentage' => $this->calculatePercentage(
                                $markData['marks_obtained'] ?? null,
                                $markData['full_mark'] ?? null
                            ),
                            'marks_grade' => $markData['grade'] ?? null,
                            'is_absent' => $markData['is_absent'] ?? false,
                            'session_id' => $this->sessionId,
                            'school_id' => $this->schoolId,
                            'is_active' => true,
                            'remarks' => $markData['remarks'] ?? null,
                        ]);
                        $savedCount++;
                    }
                }
            }
        }

        $totalCount = $savedCount + $updatedCount;
        if ($totalCount > 0) {
            session()->flash('message', "Successfully saved marks for this student ({$savedCount} new, {$updatedCount} updated).");
            $this->dispatchBrowserEvent('show-message', ['message' => "Marks saved for student!"]);
        } else {
            session()->flash('message', 'No marks to save for this student.');
        }
    }

    private function calculatePercentage($obtained, $max)
    {
        if ($obtained !== null && $max > 0) {
            return round(($obtained / $max) * 100, 2);
        }
        return null;
    }

    private function getCurrentSection($studentcrId)
    {
        $student = Bs11Studentcr::find($studentcrId);
        return $student->current_section_id ?? null;
    }

    public function render()
    {
        return view('livewire.ex30-marks-entry-comb-semesters-comp');
    }
}
