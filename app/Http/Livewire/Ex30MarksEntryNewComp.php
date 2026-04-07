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
use App\Models\Bs10Studentdb;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Ex30MarksEntryNewComp extends Component
{
    // Auto-selected values
    public $sessionId = null;
    public $schoolId = null;
    
    // User selections
    public $selectedClassId = null;
    public $selectedSubjectId = null;
    
    // Options
    public $myclasses = [];
    public $subjects = [];
    
    // Data
    public $students = [];
    public $examDetails = [];
    public $examSettings = [];
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
        $this->students = [];
        $this->examDetails = [];
        $this->examSettings = [];
        $this->marksData = [];
    }

    public function updatedSelectedSubjectId()
    {
        if ($this->selectedClassId && $this->selectedSubjectId) {
            $this->loadStudents();
            $this->loadExamDetailsAndSettings();
        }
    }

    public function loadStudents()
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId) {
            $this->students = [];
            return;
        }

        // Get students for selected class using JOIN with studentdb_subjects
        // This ensures we only get students who have the selected subject
        try {
            $this->students = Bs11Studentcr::join('bs10_studentdbs', 'bs11_studentcrs.studentdb_id', '=', 'bs10_studentdbs.id')
                ->join('bs12_studentdb_subjects', 'bs10_studentdbs.id', '=', 'bs12_studentdb_subjects.studentdb_id')
                ->where('bs11_studentcrs.session_id', $this->sessionId)
                ->where('bs11_studentcrs.school_id', $this->schoolId)
                ->where('bs11_studentcrs.current_myclass_id', $this->selectedClassId)
                ->where('bs11_studentcrs.is_active', 1)
                ->where('bs12_studentdb_subjects.subject_id', $this->selectedSubjectId)
                ->where('bs12_studentdb_subjects.is_active', 1)
                ->select('bs11_studentcrs.*', 'bs10_studentdbs.student_name', 'bs10_studentdbs.board_reg_no')
                ->distinct()
                ->orderBy('bs11_studentcrs.roll_no')
                ->with(['studentdb', 'currentMyclass', 'currentSection'])
                ->get();
            
            \Log::info('Students loaded:', [
                'count' => $this->students->count(),
                'class_id' => $this->selectedClassId,
                'subject_id' => $this->selectedSubjectId,
                'session_id' => $this->sessionId,
                'school_id' => $this->schoolId
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading students:', ['message' => $e->getMessage()]);
            $this->students = [];
        }
    }

    public function loadExamDetailsAndSettings()
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId) {
            return;
        }

        // Get all exam details for the selected class, session, and school
        $this->examDetails = Ex24Detail::where('myclass_id', $this->selectedClassId)
            ->where('session_id', $this->sessionId)
            ->where('school_id', $this->schoolId)
            ->where('is_active', 1)
            ->with(['examName', 'examType', 'examMode', 'semester'])
            ->orderBy('id')
            ->get();

        // Get exam settings for each exam detail and selected subject
        $this->examSettings = [];
        foreach ($this->examDetails as $detail) {
            $setting = Ex25Settings::where('exam_detail_id', $detail->id)
                ->where('myclass_id', $this->selectedClassId)
                ->where('subject_id', $this->selectedSubjectId)
                ->where('session_id', $this->sessionId)
                ->where('school_id', $this->schoolId)
                ->first();

            if ($setting) {
                $this->examSettings[$detail->id] = $setting;
            }
        }
    }

    public function saveSingleMark($studentcrId, $examDetailId)
    {
        try {
            $setting = $this->examSettings[$examDetailId] ?? null;
            
            if (!$setting) {
                session()->flash('error', 'No exam setting found for this combination.');
                return;
            }

            $markData = $this->marksData[$studentcrId][$examDetailId] ?? null;
            
            if (!isset($markData['marks_obtained']) && !isset($markData['is_absent'])) {
                session()->flash('error', 'Please enter marks or mark as absent.');
                return;
            }

            // Find student
            $student = $this->students->firstWhere('id', $studentcrId);
            if (!$student) {
                session()->flash('error', 'Student not found.');
                return;
            }

            // Find exam detail to get semester_id
            $examDetail = $this->examDetails->firstWhere('id', $examDetailId);
            if (!$examDetail) {
                session()->flash('error', 'Exam detail not found.');
                return;
            }

            // Check if record already exists
            $existingRecord = Ex30MarksEntry::where('studentcr_id', $studentcrId)
                ->where('myclass_id', $this->selectedClassId)
                ->where('subject_id', $this->selectedSubjectId)
                ->where('exam_detail_id', $examDetailId)
                ->where('session_id', $this->sessionId)
                ->first();

            $marksObtained = $markData['marks_obtained'] ?? null;
            $fullMark = $setting->full_mark;
            $percentage = $fullMark > 0 && $marksObtained !== null ? round(($marksObtained / $fullMark) * 100, 2) : 0;
            $grade = $this->calculateGrade($percentage);
            $isAbsent = isset($markData['is_absent']) && $markData['is_absent'];

            if ($existingRecord) {
                // Update existing record
                $existingRecord->update([
                    'marks_obtained' => $isAbsent ? null : $marksObtained,
                    'marks_percentage' => $percentage,
                    'marks_grade' => $grade,
                    'is_absent' => $isAbsent,
                    'remarks' => $markData['remarks'] ?? null,
                ]);
                session()->flash('message', "Mark updated successfully!");
            } else {
                // Create new record
                Ex30MarksEntry::create([
                    'studentcr_id' => $studentcrId,
                    'myclass_id' => $this->selectedClassId,
                    'section_id' => $student->current_section_id,
                    'subject_id' => $this->selectedSubjectId,
                    'exam_detail_id' => $examDetailId,
                    'exam_setting_id' => $setting->id,
                    'semester_id' => $examDetail->semester_id,
                    'marks_obtained' => $isAbsent ? null : $marksObtained,
                    'marks_percentage' => $percentage,
                    'marks_grade' => $grade,
                    'is_absent' => $isAbsent,
                    'session_id' => $this->sessionId,
                    'school_id' => $this->schoolId,
                    'is_active' => true,
                    'remarks' => $markData['remarks'] ?? null,
                ]);
                session()->flash('message', "Mark saved successfully!");
            }
        } catch (\Exception $e) {
            \Log::error('Error saving mark:', ['message' => $e->getMessage()]);
            session()->flash('error', 'Error saving mark: ' . $e->getMessage());
        }
    }

    public function saveAllMarks()
    {
        $this->validate();

        $savedCount = 0;
        $updatedCount = 0;
        $errors = [];

        foreach ($this->students as $student) {
            $studentcrId = $student->id;

            foreach ($this->examDetails as $detail) {
                $examDetailId = $detail->id;
                $setting = $this->examSettings[$examDetailId] ?? null;

                if (!$setting) {
                    continue; // Skip if no setting exists
                }

                $markData = $this->marksData[$studentcrId][$examDetailId] ?? null;
                
                // Skip if no data entered
                if (!isset($markData['marks_obtained']) && !isset($markData['is_absent'])) {
                    continue;
                }

                try {
                    // Check if record already exists
                    $existingRecord = Ex30MarksEntry::where('studentcr_id', $studentcrId)
                        ->where('myclass_id', $this->selectedClassId)
                        ->where('subject_id', $this->selectedSubjectId)
                        ->where('exam_detail_id', $examDetailId)
                        ->where('session_id', $this->sessionId)
                        ->first();

                    $marksObtained = $markData['marks_obtained'] ?? null;
                    $fullMark = $setting->full_mark;
                    $percentage = $fullMark > 0 && $marksObtained !== null ? round(($marksObtained / $fullMark) * 100, 2) : 0;
                    $grade = $this->calculateGrade($percentage);
                    $isAbsent = isset($markData['is_absent']) && $markData['is_absent'];

                    if ($existingRecord) {
                        $existingRecord->update([
                            'marks_obtained' => $isAbsent ? null : $marksObtained,
                            'marks_percentage' => $percentage,
                            'marks_grade' => $grade,
                            'is_absent' => $isAbsent,
                            'remarks' => $markData['remarks'] ?? null,
                        ]);
                        $updatedCount++;
                    } else {
                        Ex30MarksEntry::create([
                            'studentcr_id' => $studentcrId,
                            'myclass_id' => $this->selectedClassId,
                            'section_id' => $student->current_section_id,
                            'subject_id' => $this->selectedSubjectId,
                            'exam_detail_id' => $examDetailId,
                            'exam_setting_id' => $setting->id,
                            'semester_id' => $detail->semester_id,
                            'marks_obtained' => $isAbsent ? null : $marksObtained,
                            'marks_percentage' => $percentage,
                            'marks_grade' => $grade,
                            'is_absent' => $isAbsent,
                            'session_id' => $this->sessionId,
                            'school_id' => $this->schoolId,
                            'is_active' => true,
                            'remarks' => $markData['remarks'] ?? null,
                        ]);
                        $savedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Student ID {$studentcrId}, Exam {$examDetailId}: " . $e->getMessage();
                }
            }
        }

        if (count($errors) > 0) {
            session()->flash('error', 'Some marks had errors: ' . implode(', ', array_slice($errors, 0, 3)));
        }
        
        session()->flash('message', "Successfully saved {$savedCount} new records and updated {$updatedCount} existing records.");
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    public function render()
    {
        return view('livewire.ex30-marks-entry-new-comp');
    }
}
