<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Bs11Studentcr;
use App\Models\Bs12StudentdbSubject;
use App\Models\Bs04Myclass;
use App\Models\Bs05Semester;
use App\Models\Ex24Detail;
use App\Models\Ex25Settings;
use App\Models\Ex30MarksEntry;
use App\Models\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Ex30MarksEntryStudentcrComp extends Component
{
    public $sessionId;
    public $schoolId;
    public $selectedMyclassId = null;
    public $selectedStudentcrId = null;

    public $myclasses = [];
    public $studentcrs = [];
    public $examDetails = [];
    public $studentSubjects = [];

    public function mount()
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        $this->schoolId = (int) $user->school_id;

        $activeSession = Session::active()->first();
        $this->sessionId = $activeSession ? (int) $activeSession->id : null;

        $this->loadClasses();
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

    public function updatedSelectedMyclassId()
    {
        $this->selectedStudentcrId = null;
        $this->loadStudentcrs();
    }

    public function loadStudentcrs()
    {
        if (!$this->selectedMyclassId) {
            $this->studentcrs = [];
            return;
        }

        $this->studentcrs = Bs11Studentcr::with(['studentdb', 'currentSection'])
            ->where('session_id', $this->sessionId)
            ->where('school_id', $this->schoolId)
            ->where('current_myclass_id', $this->selectedMyclassId)
            ->where('is_active', 1)
            ->orderBy('roll_no')
            ->get();
    }

    public function openMarksEntry($studentcrId)
    {
        $this->selectedStudentcrId = $studentcrId;
        $this->loadExamDetails();
        $this->loadStudentSubjects();
    }

    public function loadExamDetails()
    {
        if (!$this->selectedMyclassId || !$this->sessionId || !$this->schoolId) {
            $this->examDetails = [];
            return;
        }

        $this->examDetails = Ex24Detail::with(['semester', 'examName', 'examType', 'examMode'])
            ->where('myclass_id', $this->selectedMyclassId)
            ->where('session_id', $this->sessionId)
            ->where('school_id', $this->schoolId)
            ->where('is_active', 1)
            ->orderBy('semester_id')
            ->orderBy('id')
            ->get();
    }

    public function loadStudentSubjects()
    {
        if (!$this->selectedStudentcrId) {
            $this->studentSubjects = [];
            return;
        }

        $studentcr = Bs11Studentcr::with('studentdb')->find($this->selectedStudentcrId);
        
        if (!$studentcr || !$studentcr->studentdb) {
            $this->studentSubjects = [];
            return;
        }

        $this->studentSubjects = Bs12StudentdbSubject::where('studentdb_id', $studentcr->studentdb_id)
            ->where('is_active', 1)
            ->with('subject')
            ->get();
    }

    public function getExamSetting($examDetailId, $subjectId)
    {
        return Ex25Settings::where('exam_detail_id', $examDetailId)
            ->where('myclass_id', $this->selectedMyclassId)
            ->where('subject_id', $subjectId)
            ->where('session_id', $this->sessionId)
            ->where('school_id', $this->schoolId)
            ->first();
    }

    public function getExistingMark($examDetailId, $subjectId)
    {
        $existingMark = Ex30MarksEntry::where('studentcr_id', $this->selectedStudentcrId)
            ->where('exam_detail_id', $examDetailId)
            ->where('subject_id', $subjectId)
            ->where('session_id', $this->sessionId)
            ->first();

        if ($existingMark) {
            return [
                'id' => $existingMark->id,
                'marks_obtained' => $existingMark->marks_obtained,
                'is_absent' => (bool) $existingMark->is_absent,
            ];
        }
        
        return ['id' => null, 'marks_obtained' => null, 'is_absent' => false];
    }

    public function saveMark($examDetailId, $subjectId, $marksObtained, $isAbsent)
    {
        // Ensure exam details and student subjects are loaded
        if (empty($this->examDetails)) {
            $this->loadExamDetails();
        }
        if (empty($this->studentSubjects)) {
            $this->loadStudentSubjects();
        }

        // Get exam setting directly from database
        $setting = $this->getExamSetting($examDetailId, $subjectId);

        if (!$setting) {
            Log::warning('Exam setting not found', [
                'exam_detail_id' => $examDetailId,
                'subject_id' => $subjectId,
                'myclass_id' => $this->selectedMyclassId,
                'session_id' => $this->sessionId,
                'school_id' => $this->schoolId
            ]);
            return false;
        }

        // Get student
        $studentcr = Bs11Studentcr::find($this->selectedStudentcrId);
        if (!$studentcr) {
            Log::warning('Student not found', ['studentcr_id' => $this->selectedStudentcrId]);
            return false;
        }

        // Get exam detail
        $examDetail = null;
        foreach ($this->examDetails as $detail) {
            if ($detail->id == $examDetailId) {
                $examDetail = $detail;
                break;
            }
        }

        if (!$examDetail) {
            Log::warning('Exam detail not found', ['exam_detail_id' => $examDetailId]);
            return false;
        }

        // Process marks
        $isAbsentBool = false;
        if ($isAbsent === true || $isAbsent === 'true' || $isAbsent == 1 || $isAbsent === 'on') {
            $isAbsentBool = true;
        }

        $marks = null;
        if (!$isAbsentBool && $marksObtained !== null && $marksObtained !== '') {
            $marks = (float) $marksObtained;
        }

        // Calculate percentage and grade
        $fullMark = $setting->full_mark;
        $percentage = ($fullMark > 0 && $marks !== null) 
            ? round(($marks / $fullMark) * 100, 2) 
            : 0;
        $grade = $this->calculateGrade($percentage);

        // Check if record exists
        $existingRecord = Ex30MarksEntry::where('studentcr_id', $this->selectedStudentcrId)
            ->where('exam_detail_id', $examDetailId)
            ->where('subject_id', $subjectId)
            ->where('session_id', $this->sessionId)
            ->first();

        if ($existingRecord) {
            $existingRecord->update([
                'marks_obtained' => $marks,
                'marks_percentage' => $percentage,
                'marks_grade' => $grade,
                'is_absent' => $isAbsentBool,
            ]);
        } else {
            Ex30MarksEntry::create([
                'studentcr_id' => $this->selectedStudentcrId,
                'myclass_id' => $this->selectedMyclassId,
                'section_id' => $studentcr->current_section_id ?? 0,
                'semester_id' => $examDetail->semester_id,
                'subject_id' => $subjectId,
                'exam_detail_id' => $examDetailId,
                'exam_setting_id' => $setting->id,
                'marks_obtained' => $marks,
                'marks_percentage' => $percentage,
                'marks_grade' => $grade,
                'is_absent' => $isAbsentBool,
                'session_id' => $this->sessionId,
                'school_id' => $this->schoolId,
                'is_active' => true,
            ]);
        }

        return true;
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

    public function closeMarksEntry()
    {
        $this->selectedStudentcrId = null;
    }

    public function render()
    {
        return view('livewire.ex30-marks-entry-studentcr-comp');
    }
}