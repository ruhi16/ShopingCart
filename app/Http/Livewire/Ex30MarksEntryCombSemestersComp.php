<?php

namespace App\Http\Livewire;

use App\Models\Bs04Myclass;
use App\Models\Bs07Subject;
use App\Models\Bs11Studentcr;
use App\Models\Bs05Semester;
use App\Models\Bs09MyclassSemester;
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
            $classes = Bs04Myclass::where('session_id', $this->sessionId)
                ->where('school_id', $this->schoolId)
                ->where('is_active', 1)
                ->distinct()
                ->orderBy('name')
                ->get();
            
            // Ensure uniqueness
            $this->myclasses = $classes->unique('id')->values();
            
            \Log::info('Classes loaded:', [
                'raw_count' => $classes->count(),
                'unique_count' => $this->myclasses->count(),
                'ids' => $this->myclasses->pluck('id')->toArray()
            ]);
        } else {
            $this->myclasses = collect();
        }
    }

    public function loadSubjects()
    {
        if ($this->sessionId && $this->schoolId) {
            $subjects = Bs07Subject::where('session_id', $this->sessionId)
                ->where('school_id', $this->schoolId)
                ->where('is_active', 1)
                ->distinct()
                ->orderBy('name')
                ->get();
            
            // Ensure uniqueness
            $this->subjects = $subjects->unique('id')->values();
            
            \Log::info('Subjects loaded:', [
                'raw_count' => $subjects->count(),
                'unique_count' => $this->subjects->count(),
                'ids' => $this->subjects->pluck('id')->toArray()
            ]);
        } else {
            $this->subjects = collect();
        }
    }

    public function updatedSelectedClassId()
    {
        // Only reset subject selection and marks data, keep session and school
        $this->selectedSubjectId = null;
        // Reload subjects with deduplication
        $this->subjects = Bs07Subject::where('session_id', $this->sessionId)
            ->where('school_id', $this->schoolId)
            ->where('is_active', 1)
            ->distinct()
            ->orderBy('name')
            ->get()
            ->unique('id')
            ->values();
        $this->semesters = collect();
        $this->examDetailsBySemester = [];
        $this->examSettingsBySemester = [];
        $this->students = collect();
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
            $this->students = collect();
            return;
        }

        // Get students for selected class who are enrolled in the selected subject
        $students = Bs11Studentcr::with(['studentdb'])
            ->where('current_myclass_id', $this->selectedClassId)
            ->where('session_id', $this->sessionId)
            ->where('is_active', 1)
            ->orderBy('roll_no')
            ->get();
        
        \Log::info('Students before filtering:', [
            'count' => $students->count(),
            'student_ids' => $students->pluck('id')->toArray()
        ]);
        
        // Filter students who are enrolled in the selected subject via bs12_studentdb_subjects
        $filteredStudents = $students->filter(function($student) {
            if (!$student->studentdb) {
                return false;
            }
            
            // Check if student has the selected subject in bs12_studentdb_subjects
            $hasSubject = \DB::table('bs12_studentdb_subjects')
                ->where('studentdb_id', $student->studentdb_id)
                ->where('subject_id', $this->selectedSubjectId)
                ->where('is_active', 1)
                ->exists();
            
            return $hasSubject;
        });
        
        // Map to add board_reg_no and ensure uniqueness
        $this->students = $filteredStudents
            ->map(function($student) {
                $student->board_reg_no = $student->studentdb->board_reg_no ?? '';
                return $student;
            })
            ->sortBy('board_reg_no')
            ->values(); // Single call to values()
        
        \Log::info('Students after filtering:', [
            'count' => $this->students->count(),
            'student_ids' => $this->students->pluck('id')->toArray(),
            'selected_subject_id' => $this->selectedSubjectId
        ]);

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
        
        // Use semesterClasses relationship (Bs09MyclassSemester) which links classes to semesters
        // Get unique semester IDs first to avoid duplicates
        $semesterIds = Bs09MyclassSemester::where('myclass_id', $selectedClassId)
            ->pluck('semester_id')
            ->unique()
            ->toArray();
        
        // Then fetch the actual semester records
        if (!empty($semesterIds)) {
            $this->semesters = Bs05Semester::whereIn('id', $semesterIds)
                ->orderBy('id')
                ->get();
        } else {
            $this->semesters = collect();
        }

        // If no semesters found through relationships, get all active semesters
        if ($this->semesters->isEmpty()) {
            $this->semesters = Bs05Semester::active()->get();
        }
        
        // Log for debugging
        \Log::info('Semesters loaded:', [
            'count' => $this->semesters->count(),
            'semester_ids' => $this->semesters->pluck('id')->toArray()
        ]);

        // For each semester, get exam details and settings
        foreach ($this->semesters as $semester) {
            $semesterId = $semester->id;

            // Get exam details for this class, semester, and session - ensure unique
            $examDetails = Ex24Detail::where('myclass_id', $this->selectedClassId)
                ->where('semester_id', $semesterId)
                ->where('session_id', $this->sessionId)
                ->where('is_active', 1)
                ->distinct()
                ->orderBy('id')
                ->get();
            
            // Ensure no duplicate exam details by ID
            $examDetails = $examDetails->unique('id')->values();

            $this->examDetailsBySemester[$semesterId] = $examDetails;
            
            \Log::info("Exam details for semester {$semesterId}:", [
                'count' => $examDetails->count(),
                'detail_ids' => $examDetails->pluck('id')->toArray(),
                'unique_count' => $examDetails->unique('id')->count()
            ]);

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
        
        // Final verification - log all data structures
        \Log::info('Final data structures:', [
            'semesters_count' => $this->semesters->count(),
            'semesters' => $this->semesters->pluck('id')->toArray(),
            'exam_details_by_semester' => collect($this->examDetailsBySemester)->map(function($details) {
                return $details->pluck('id')->toArray();
            })->toArray()
        ]);
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
                            // Create new record - don't include 'id' as it's auto-incrementing
                            $markEntry = new Ex30MarksEntry();
                            $markEntry->studentcr_id = $studentcrId;
                            $markEntry->myclass_id = $this->selectedClassId;
                            $markEntry->section_id = $this->getCurrentSection($studentcrId);
                            $markEntry->semester_id = $semesterId;
                            $markEntry->subject_id = $this->selectedSubjectId;
                            $markEntry->exam_detail_id = $examDetailId;
                            $markEntry->exam_setting_id = $markData['exam_setting_id'] ?? null;
                            $markEntry->marks_obtained = $markData['marks_obtained'] ?? null;
                            $markEntry->marks_percentage = $this->calculatePercentage(
                                $markData['marks_obtained'] ?? null,
                                $markData['full_mark'] ?? null
                            );
                            $markEntry->marks_grade = $markData['grade'] ?? null;
                            $markEntry->is_absent = $markData['is_absent'] ?? false;
                            $markEntry->session_id = $this->sessionId;
                            $markEntry->school_id = $this->schoolId;
                            $markEntry->is_active = true;
                            $markEntry->remarks = $markData['remarks'] ?? null;
                            $markEntry->save();
                            
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
                        // Create new record - don't include 'id' as it's auto-incrementing
                        $markEntry = new Ex30MarksEntry();
                        $markEntry->studentcr_id = $studentId;
                        $markEntry->myclass_id = $this->selectedClassId;
                        $markEntry->section_id = $this->getCurrentSection($studentId);
                        $markEntry->semester_id = $semesterId;
                        $markEntry->subject_id = $this->selectedSubjectId;
                        $markEntry->exam_detail_id = $examDetailId;
                        $markEntry->exam_setting_id = $markData['exam_setting_id'] ?? null;
                        $markEntry->marks_obtained = $markData['marks_obtained'] ?? null;
                        $markEntry->marks_percentage = $this->calculatePercentage(
                            $markData['marks_obtained'] ?? null,
                            $markData['full_mark'] ?? null
                        );
                        $markEntry->marks_grade = $markData['grade'] ?? null;
                        $markEntry->is_absent = $markData['is_absent'] ?? false;
                        $markEntry->session_id = $this->sessionId;
                        $markEntry->school_id = $this->schoolId;
                        $markEntry->is_active = true;
                        $markEntry->remarks = $markData['remarks'] ?? null;
                        $markEntry->save();
                        
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
