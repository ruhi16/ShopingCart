<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Bs04Myclass;
use App\Models\Bs05Semester;
use App\Models\Ex24Detail;
use App\Models\Bs07Subject;
use App\Models\Ex25Settings;
use App\Models\Bs11Studentcr;
use App\Models\Ex30MarksEntry;
use App\Models\Session;


class Ex30MarksEntryComp3 extends Component
{
    // Filter IDs
    public $myclass_id;
    public $semester_id;
    public $exam_detail_id;
    public $subject_id;

    // Dropdown options
    public $myclasses = [];
    public $semesters = [];
    public $exams = [];
    public $subjects = [];

    // Students list and marks data
    public $students = [];
    public $marks = []; // [studentcr_id => marks]
    public $absent = []; // [studentcr_id => boolean]

    public function mount()
    {
        $this->myclasses = Bs04Myclass::active()->get();
        $this->semesters = Bs05Semester::active()->get();
    }

    public function updatedMyclassId()
    {
        $this->resetSelections(['exam_detail_id', 'subject_id']);
        $this->loadExamsAndSubjects();
        $this->loadStudents();
    }

    public function updatedSemesterId()
    {
        $this->loadExamsAndSubjects();
        $this->loadStudents();
    }

    public function updatedExamDetailId()
    {
        $this->loadStudents();
    }

    public function updatedSubjectId()
    {
        $this->loadStudents();
    }

    public function loadExamsAndSubjects()
    {
        if ($this->myclass_id) {
            $class = Bs04Myclass::find($this->myclass_id);
            $session_id = Session::active()->first()->id;
            if ($class) {
                // Load all active exams for the class's session and school
                $this->exams = Ex24Detail::where('session_id', $session_id)
                    ->where('school_id', $class->school_id)
                    ->where('is_active', 1)
                    ->where('myclass_id', $this->myclass_id)
                    ->where('semester_id', $this->semester_id)
                    ->get();

                // Load all active subjects for the class's session and school
                $this->subjects = Bs07Subject::where('session_id', $class->session_id)
                    ->where('school_id', $class->school_id)
                    ->where('is_active', 1)
                    ->get();
            }
        } else {
            $this->exams = [];
            $this->subjects = [];
        }
    }

    public function loadStudents()
    {
        if ($this->myclass_id && $this->subject_id && $this->exam_detail_id && $this->semester_id) {
            // Find students who are enrolled in this class and have this subject in studentdb_subjects
            $this->students = Bs11Studentcr::with(['studentdb'])
                ->join('bs10_studentdbs', 'bs11_studentcrs.studentdb_id', '=', 'bs10_studentdbs.id')
                ->where('bs11_studentcrs.current_myclass_id', $this->myclass_id)
                ->whereHas('studentdb.studentSubjects', function ($query) {
                    $query->where('subject_id', $this->subject_id);
                })
                ->orderBy('bs10_studentdbs.board_reg_no')
                ->select('bs11_studentcrs.*')
                ->get();

            // Load existing marks
            $this->marks = [];
            $this->absent = [];

            $existingMarks = Ex30MarksEntry::where('myclass_id', $this->myclass_id)
                ->where('semester_id', $this->semester_id)
                ->where('exam_detail_id', $this->exam_detail_id)
                ->where('subject_id', $this->subject_id)
                ->get();

            foreach ($existingMarks as $m) {
                $this->marks[$m->studentcr_id] = $m->is_absent ? 'AB' : $m->marks_obtained;
                $this->absent[$m->studentcr_id] = $m->is_absent ? true : false;
            }
        } else {
            $this->students = [];
        }
    }

    public function toggleAbsent($studentcr_id)
    {
        if ($this->absent[$studentcr_id]) {
            $this->marks[$studentcr_id] = 'AB';
        } else {
            $this->marks[$studentcr_id] = '';
        }
        $this->saveMark($studentcr_id);
    }

    public function saveMark($studentcr_id)
    {
        $setting = Ex25Settings::where('myclass_id', $this->myclass_id)
            ->where('semester_id', $this->semester_id)
            ->where('exam_detail_id', $this->exam_detail_id)
            ->where('subject_id', $this->subject_id)
            ->first();

        if (!$setting) return;

        $studentcr = Bs11Studentcr::find($studentcr_id);

        $is_absent = isset($this->absent[$studentcr_id]) && $this->absent[$studentcr_id];
        $mark_val = $this->marks[$studentcr_id] ?? null;

        if ($is_absent) {
            $mark_val = null;
        }

        Ex30MarksEntry::updateOrCreate(
            [
                'studentcr_id' => $studentcr_id,
                'myclass_id' => $this->myclass_id,
                'semester_id' => $this->semester_id,
                'exam_detail_id' => $this->exam_detail_id,
                'subject_id' => $this->subject_id,
            ],
            [
                'section_id' => $studentcr->current_section_id ?? 0,
                'exam_setting_id' => $setting->id,
                'marks_obtained' => is_numeric($mark_val) ? $mark_val : null,
                'is_absent' => $is_absent ? 1 : 0,
                'session_id' => $studentcr->session_id,
                'school_id' => $studentcr->school_id,
            ]
        );
    }

    private function resetSelections($fields)
    {
        foreach ($fields as $field) {
            $this->$field = null;
        }
        if (in_array('exam_detail_id', $fields)) $this->exams = [];
        if (in_array('subject_id', $fields)) $this->subjects = [];
        $this->students = [];
    }

    public function render()
    {
        return view('livewire.ex30-marks-entry-comp3');
    }
}
