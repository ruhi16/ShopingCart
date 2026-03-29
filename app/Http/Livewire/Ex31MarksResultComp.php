<?php

namespace App\Http\Livewire;

use App\Models\Bs11Studentcr;
use App\Models\Bs12StudentdbSubject;
use App\Models\Ex25Settings;
use App\Models\Ex24Detail;
use App\Models\Ex30MarksEntry;
use App\Models\Session;
use App\Models\Bs04Myclass;
use Livewire\Component;

class Ex31MarksResultComp extends Component
{
    public $session_id;
    public $myclass_id;
    public $studentcr_id;
    
    public $student = null;
    public $subjects = [];
    public $examDetails = [];
    public $marksData = [];
    public $totalMarks = 0;
    public $totalFullMarks = 0;
    public $percentage = 0;
    public $grade = 'N/A';
    public $resultStatus = '';

    public function mount($sessionId = null, $myclassId = null, $studentcrId = null)
    {
        // Use route parameters or fall back to properties
        if ($sessionId) $this->session_id = $sessionId;
        if ($myclassId) $this->myclass_id = $myclassId;
        if ($studentcrId) $this->studentcr_id = $studentcrId;
        
        $this->loadResultData();
    }

    public function render()
    {
        return view('livewire.ex31-marks-result-comp');
    }

    private function loadResultData()
    {
        if (!$this->session_id || !$this->myclass_id || !$this->studentcr_id) {
            return;
        }

        // Load studentcr with relationships
        $this->student = Bs11Studentcr::with([
            'studentdb',
            'currentMyclass',
            'currentSection',
            'currentSemester'
        ])->findOrFail($this->studentcr_id);

        // Verify student belongs to the selected class and session
        if ($this->student->session_id != $this->session_id || 
            $this->student->current_myclass_id != $this->myclass_id) {
            abort(404, 'Student not found in this class/session');
        }

        // Get subjects opted by this student
        $studentSubjects = Bs12StudentdbSubject::with(['subject'])
            ->where('studentdb_id', $this->student->studentdb_id)
            ->where('is_active', 1)
            ->get();

        $this->subjects = $studentSubjects->pluck('subject')->toArray();
        $subjectIds = $studentSubjects->pluck('subject_id')->toArray();

        // Get all exam details for this class (all semesters)
        $semesterIds = Bs11Studentcr::where('session_id', $this->session_id)
            ->where('school_id', $this->student->school_id)
            ->where('current_myclass_id', $this->myclass_id)
            ->where('is_active', 1)
            ->whereNotNull('current_semester_id')
            ->distinct()
            ->pluck('current_semester_id');

        $examSettings = Ex25Settings::with(['examDetail'])
            ->where('myclass_id', $this->myclass_id)
            ->whereIn('semester_id', $semesterIds)
            ->where('session_id', $this->session_id)
            ->where('school_id', $this->student->school_id)
            ->where('is_active', 1)
            ->get();

        $examSettingsByDetail = $examSettings->groupBy('exam_detail_id');
        $examDetailIds = $examSettingsByDetail->keys()->toArray();

        $this->examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
            ->whereIn('id', $examDetailIds)
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get();

        // Get marks for this student
        $allMarks = Ex30MarksEntry::where('studentcr_id', $this->studentcr_id)
            ->where('session_id', $this->session_id)
            ->where('school_id', $this->student->school_id)
            ->get();

        // Build marks data organized by subject and exam
        $this->marksData = [];
        $this->totalMarks = 0;
        $this->totalFullMarks = 0;

        foreach ($studentSubjects as $studentSubject) {
            $subjectId = $studentSubject->subject_id;
            $this->marksData[$subjectId] = [
                'subject' => $studentSubject->subject,
                'exam_marks' => [],
                'subject_total' => 0,
                'subject_full' => 0,
                'subject_percentage' => 0,
                'subject_grade' => 'N/A',
            ];

            foreach ($this->examDetails as $examDetail) {
                $examDetailId = $examDetail->id;
                
                // Find exam setting for this subject and exam detail
                $settingsForDetail = $examSettingsByDetail->get($examDetailId, collect());
                $setting = $settingsForDetail->firstWhere('subject_id', $subjectId);

                // Find marks entry
                $marksEntry = $allMarks->first(function($mark) use ($examDetailId, $subjectId) {
                    return $mark->exam_detail_id == $examDetailId && $mark->subject_id == $subjectId;
                });

                $fullMark = $setting ? $setting->full_mark : 0;
                $passMark = $setting ? $setting->pass_mark : 0;

                if ($marksEntry) {
                    $marksObtained = $marksEntry->marks_obtained;
                    $isAbsent = ($marksEntry->is_absent ?? ($marksObtained == -99));
                    $grade = $marksEntry->marks_grade;
                    $percentage = $marksEntry->marks_percentage;

                    if ($isAbsent) {
                        $marksObtained = null;
                    }

                    if ($marksObtained !== null) {
                        $this->marksData[$subjectId]['subject_total'] += $marksObtained;
                        $this->totalMarks += $marksObtained;
                    }

                    $this->marksData[$subjectId]['subject_full'] += $fullMark;
                    $this->totalFullMarks += $fullMark;

                    $this->marksData[$subjectId]['exam_marks'][$examDetailId] = [
                        'exam_detail' => $examDetail,
                        'marks_obtained' => $isAbsent ? null : $marksObtained,
                        'full_mark' => $fullMark,
                        'pass_mark' => $passMark,
                        'is_absent' => $isAbsent,
                        'grade' => $grade,
                        'percentage' => $percentage,
                    ];
                } else {
                    $this->marksData[$subjectId]['subject_full'] += $fullMark;
                    $this->totalFullMarks += $fullMark;

                    $this->marksData[$subjectId]['exam_marks'][$examDetailId] = [
                        'exam_detail' => $examDetail,
                        'marks_obtained' => null,
                        'full_mark' => $fullMark,
                        'pass_mark' => $passMark,
                        'is_absent' => false,
                        'grade' => 'N/A',
                        'percentage' => 0,
                    ];
                }
            }

            // Calculate subject total and grade
            if ($this->marksData[$subjectId]['subject_full'] > 0) {
                $this->marksData[$subjectId]['subject_percentage'] = round(
                    ($this->marksData[$subjectId]['subject_total'] / $this->marksData[$subjectId]['subject_full']) * 100, 
                    2
                );
                $this->marksData[$subjectId]['subject_grade'] = $this->calculateGrade(
                    $this->marksData[$subjectId]['subject_percentage']
                );
            }
        }

        // Calculate overall total and grade
        if ($this->totalFullMarks > 0) {
            $this->percentage = round(($this->totalMarks / $this->totalFullMarks) * 100, 2);
            $this->grade = $this->calculateGrade($this->percentage);
            
            // Determine pass/fail status
            $hasFailed = false;
            foreach ($this->marksData as $subjectData) {
                if ($subjectData['subject_grade'] == 'F') {
                    $hasFailed = true;
                    break;
                }
            }
            
            $this->resultStatus = $hasFailed ? 'FAIL' : 'PASS';
        }
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
}
