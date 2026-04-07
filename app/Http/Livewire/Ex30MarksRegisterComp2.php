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
use PDF;


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

    public function generatePdf($myclassId = null)
    {
        ini_set('max_execution_time', 300);
        // Resolve IDs from auth and active session (not from component state)
        $activeSession = Session::active()->first();
        $sessionId = $activeSession ? $activeSession->id : null;
        $schoolId = auth()->user()->school_id;
        $myclassId = $myclassId ?: $this->selected_myclass_id ?: request()->route('myclassId');

        if (!$sessionId || !$schoolId || $myclassId === null) {
            abort(400, 'Missing required parameters for PDF generation.');
        }

        // Fetch school, session, class
        $school  = School::find($schoolId);
        $session = Session::find($sessionId);
        $myclass = Bs04Myclass::find($myclassId);

        // 1. Get students
        $students = Bs11Studentcr::with(['studentdb', 'currentMyclass', 'currentSection', 'currentSemester'])
            ->where('session_id', $sessionId)
            ->where('school_id', $schoolId)
            ->where('current_myclass_id', $myclassId)
            ->where('is_active', 1)
            ->orderBy('roll_no', 'asc')
            ->get();

        $examDetails = collect();
        $examSettingsByDetail = collect();
        $studentSubjects = collect();
        $allMarks = collect();

        if ($students->isNotEmpty()) {
            // 2. Exam settings
            $allowedSemesters = Bs09MyclassSemester::where('myclass_id', $myclassId)
                ->where('session_id', $sessionId)
                ->where('school_id', $schoolId)
                ->pluck('semester_id');

            $examSettings = Ex25Settings::with(['examDetail', 'subject'])
                ->where('myclass_id', $myclassId)
                ->whereIn('semester_id', $allowedSemesters)
                ->where('session_id', $sessionId)
                ->where('school_id', $schoolId)
                ->where('is_active', 1)
                ->get();

            $examSettingsByDetail = $examSettings->groupBy('exam_detail_id');
            $examDetailIds = $examSettingsByDetail->keys();

            $examDetails = Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode', 'semester'])
                ->whereIn('id', $examDetailIds)
                ->where('is_active', 1)
                ->orderBy('id', 'ASC')
                ->get();

            // 3. Student subjects
            $studentdbIds = $students->pluck('studentdb_id')->unique();
            $studentSubjects = Bs12StudentdbSubject::with(['subject'])
                ->whereIn('studentdb_id', $studentdbIds)
                ->where('is_active', 1)
                ->get()
                ->groupBy('studentdb_id');

            // 4. All marks entries
            $studentcrIds = $students->pluck('id');
            $allMarks = Ex30MarksEntry::whereIn('studentcr_id', $studentcrIds)
                ->where('session_id', $sessionId)
                ->where('school_id', $schoolId)
                ->get()
                ->groupBy('studentcr_id');
        }

        // 5. Compute footer stats inline while building marks data per student
        $totalStudents  = $students->count();
        $totalMarkCount = 0;
        $sumPct         = 0;
        $passCount      = 0;

        // Pre-build exam column headers text
        $examColHeaders = '';
        foreach ($examDetails as $ed) {
            $examColHeaders .= '<th style="text-align:center;background:#e8f0fe;color:#1a3a6c;border:1px solid #c5d5e8;padding:3px 4px;font-size:10px;font-weight:600">'
                . e($ed->examName->name ?? '-')
                . '<br><span style="font-size:8px;color:#5a7ab5;font-weight:normal">'
                . e(($ed->semester->name ?? '') . ' · ' . ($ed->examType->name ?? '') . ' · ' . ($ed->examMode->name ?? ''))
                . '</span></th>';
        }
        $examColCount = count($examDetails);

        // Start building HTML chunks
        $chunks = [];

        // Styles and header chunk
        $schoolName = $school ? e($school->name) : 'School';
        $className  = $myclass ? e($myclass->name) : '-';
        $sessionName = $session ? e($session->name) : '-';

        $chunks[] = '<!DOCTYPE html><html><head><meta charset="UTF-8">'
            . '<style>'
            . 'body{font-family:sans-serif;font-size:11px;margin:0;padding:8px}'
            . 'table{width:100%;border-collapse:collapse}'
            . 'th,td{border:1px solid #c5d5e8;padding:3px 4px;vertical-align:top}'
            . '.sn{font-weight:600;color:#222;font-size:10px}'
            . '.sc{color:#999;font-size:8px}'
            . '.snb{display:inline-block;padding:0 4px;border-radius:2px;font-size:9px;font-weight:500}'
            . '.snorm{background:#f0e6f6;color:#6b21a8}'
            . '.saddl{background:#fef3c7;color:#92400e}'
            . '.at{font-size:7px;background:#fde68a;color:#78350f;padding:0 2px;border-radius:2px;margin-left:1px}'
            . '.mo{font-weight:bold;color:#1e40af}'
            . '.mf{color:#9ca3af}'
            . '.ab{color:#b91c1c;background:#fee2e2;padding:0 3px;border-radius:2px;font-weight:500;font-size:9px}'
            . '.ns{color:#d1d5db}'
            . '.stc{background:#ecfdf5;text-align:center}'
            . '.sto{font-weight:600;color:#166534;font-size:10px}'
            . '.stp{color:#15803d;font-size:8px}'
            . '.gb{display:inline-block;padding:0 4px;border-radius:2px;font-size:8px;font-weight:600;margin-top:1px}'
            . '.gA-p{background:#bfdbfe;color:#1e3a8a}.gA{background:#dbeafe;color:#1e40af}'
            . '.gA-m{background:#e0e7ff;color:#3730a3}.gB{background:#ccfbf1;color:#115e59}'
            . '.gC{background:#fef9c3;color:#854d0e}.gD{background:#ffedd5;color:#9a3412}'
            . '.gF{background:#fee2e2;color:#991b1b}'
            . '.ovc{background:#eff6ff;text-align:center;border-left:2px solid #93c5fd;vertical-align:middle}'
            . '.ovo{font-weight:bold;color:#1e3a8a;font-size:11px}'
            . '.ovp{color:#2563eb;font-size:9px}'
            . '.rp{display:inline-block;background:#bbf7d0;color:#166534;padding:0 5px;border-radius:2px;font-size:8px;font-weight:600;margin-top:1px}'
            . '.rf{display:inline-block;background:#fecaca;color:#991b1b;padding:0 5px;border-radius:2px;font-size:8px;font-weight:600;margin-top:1px}'
            . '.re{background:#f9fafb}.rfi{border-top:2px solid #d1d5db}'
            . 'tfoot td{background:#e8f0fe;border-top:2px solid #93c5fd;font-size:9px;color:#1a3a6c}'
            . '.fa{background:#ecfdf5;color:#166534;font-weight:600}'
            . '.fp{background:#dbeafe;color:#1e3a8a;font-weight:600;border-left:2px solid #93c5fd}'
            . '</style></head><body>'
            . '<div style="text-align:center;margin-bottom:8px">'
            . '<h1 style="font-size:16px;margin:0;color:#1e3a5f">' . $schoolName . '</h1>'
            . '<p style="font-size:13px;color:#333;margin:1px 0">Marks Register</p>'
            . '<p style="font-size:9px;color:#666;margin:0">Class: <b>' . $className . '</b> | Session: <b>' . $sessionName . '</b></p>'
            . '</div>';

        if ($totalStudents > 0 && $examColCount > 0) {
            // Table header chunk
            $chunks[] = '<table><thead><tr>'
                . '<th style="text-align:left;background:#e8f0fe;color:#1a3a6c;border:1px solid #c5d5e8;padding:3px 4px;font-size:10px;font-weight:600;width:25px">Roll</th>'
                . '<th style="text-align:left;background:#e8f0fe;color:#1a3a6c;border:1px solid #c5d5e8;padding:3px 4px;font-size:10px;font-weight:600;width:100px">Student</th>'
                . '<th style="text-align:left;background:#e8f0fe;color:#1a3a6c;border:1px solid #c5d5e8;padding:3px 4px;font-size:10px;font-weight:600;width:60px">Subject</th>'
                . $examColHeaders
                . '<th style="text-align:center;background:#ecfdf5;color:#166534;border:1px solid #c5d5e8;padding:3px 4px;font-size:10px;font-weight:600;width:60px">Sub Total</th>'
                . '<th style="text-align:center;background:#dbeafe;color:#1e3a8a;border:1px solid #c5d5e8;border-left:2px solid #93c5fd;padding:3px 4px;font-size:10px;font-weight:600;width:70px">Overall / Result</th>'
                . '</tr></thead><tbody>';

            // Student rows - write one student at a time
            foreach ($students as $student) {
                $studentcrId = $student->id;
                $studentdbId = $student->studentdb_id;
                $rollNo = e($student->roll_no);
                $studentName = e($student->studentdb->student_name ?? '-');
                $studentRegNo = e($student->studentdb->board_reg_no ?? '-');
                
                $studentClass = e(($student->currentMyclass->name ?? '') . ' ' . ($student->currentSection->name ?? ''));

                $subjectsOpted = $studentSubjects->get($studentdbId, collect());
                $subjectCount = $subjectsOpted->count();
                $rowspan = max(1, $subjectCount);

                // Find additional subject
                $subjectTotals = [];
                foreach ($subjectsOpted as $so) {
                    $sid = $so->subject_id;
                    $obt = 0;
                    $marksEntry = $allMarks->get($studentcrId, collect())
                        ->where('subject_id', $sid);
                    foreach ($marksEntry as $me) {
                        if ($me->marks_obtained !== null && !($me->is_absent ?? ($me->marks_obtained == -99))) {
                            $obt += $me->marks_obtained;
                        }
                    }
                    $subjectTotals[$sid] = $obt;
                }
                $additionalSubjectId = null;
                if (!empty($subjectTotals)) {
                    $minVal = min($subjectTotals);
                    foreach ($subjectTotals as $sid => $val) {
                        if ($val == $minVal) { $additionalSubjectId = $sid; break; }
                    }
                }

                // Overall total
                $overallObt = 0;
                $overallFull = 500;
                foreach ($subjectsOpted as $so) {
                    $sid = $so->subject_id;
                    if ($sid == $additionalSubjectId) continue;
                    foreach ($allMarks->get($studentcrId, collect())->where('subject_id', $sid) as $me) {
                        if ($me->marks_obtained !== null && !($me->is_absent ?? ($me->marks_obtained == -99))) {
                            $overallObt += $me->marks_obtained;
                        }
                    }
                }
                $overallPct = $overallFull > 0 ? round(($overallObt / $overallFull) * 100, 1) : 0;
                $overallGrade = $this->calculateGrade($overallPct);
                $overallPass = $overallPct >= 40;
                $overallGradeClass = $this->gradeCssClass($overallGrade);

                if ($subjectCount > 0) {
                    $subjIndex = 0;
                    foreach ($subjectsOpted as $studentSubject) {
                        $subjectId = $studentSubject->subject_id;
                        $subjName = $studentSubject->subject->short_name ?? $studentSubject->subject->name ?? 'Subj';
                        $isFirst = ($subjIndex === 0);
                        $isAdditional = ($subjectId == $additionalSubjectId);

                        // Subject sub-total
                        $subjObt = 0;
                        $subjFull = 100;
                        foreach ($examDetails as $ed) {
                            $me = $allMarks->get($studentcrId, collect())
                                ->where('exam_detail_id', $ed->id)
                                ->where('subject_id', $subjectId)
                                ->first();
                            if ($me && $me->marks_obtained !== null && !($me->is_absent ?? false)) {
                                $subjObt += $me->marks_obtained;
                            }
                        }
                        $subjPct = $subjFull > 0 ? round(($subjObt / $subjFull) * 100, 1) : 0;
                        $subjGrade = $this->calculateGrade($subjPct);
                        $subjGradeClass = $this->gradeCssClass($subjGrade);

                        // Update global stats
                        foreach ($examDetails as $ed) {
                            $me = $allMarks->get($studentcrId, collect())
                                ->where('exam_detail_id', $ed->id)
                                ->where('subject_id', $subjectId)
                                ->first();
                            if ($me && $me->marks_obtained !== null && !($me->is_absent ?? false)) {
                                $totalMarkCount++;
                                $pct = $ed->full_mark > 0 ? round(($me->marks_obtained / $ed->full_mark) * 100, 1) : 0;
                                $sumPct += $pct;
                                if ($pct >= 40) $passCount++;
                            }
                        }

                        $rowClass = $isFirst ? 'rfi' : ($subjIndex % 2 == 0 ? 're' : '');
                        $chunk = '<tr class="' . $rowClass . '">';

                        if ($isFirst) {
                            $chunk .= '<td rowspan="' . $rowspan . '" style="text-align:center;color:#888;font-weight:500;vertical-align:middle">' . $rollNo . '</td>';
                            $chunk .= '<td rowspan="' . $rowspan . '" style="vertical-align:middle"><div class="sn">' . $studentName . '</div><div class="sc">Class: ' . $studentClass . '</div><div class="sc">Reg No: ' . $studentRegNo . '</div></td>';
                        }

                        $chunk .= '<td><span class="snb ' . ($isAdditional ? 'saddl' : 'snorm') . '">' . e($subjName) . '</span>';
                        if ($isAdditional) {
                            $chunk .= '<span class="at">Addl</span>';
                        }
                        $chunk .= '</td>';

                        // Exam marks cells
                        foreach ($examDetails as $ed) {
                            $me = $allMarks->get($studentcrId, collect())
                                ->where('exam_detail_id', $ed->id)
                                ->where('subject_id', $subjectId)
                                ->first();

                            $setting = $examSettingsByDetail->get($ed->id, collect())
                                ->firstWhere('subject_id', $subjectId);

                            $hasSetting = $setting !== null;
                            $isAbsent = $me && ($me->is_absent ?? ($me->marks_obtained == -99));
                            $hasMarks = $me && $me->marks_obtained !== null;

                            $chunk .= '<td style="text-align:center' . ($isAbsent ? ';background:#fef2f2' : '') . '">';

                            if (!$hasSetting) {
                                $chunk .= '<span class="ns">—</span>';
                            } elseif ($isAbsent) {
                                $chunk .= '<span class="ab">AB</span>';
                            } elseif ($hasMarks) {
                                $chunk .= '<span class="mo">' . (int) round($me->marks_obtained) . '</span><span class="mf">/' . ($setting->full_mark ?? 0) . '</span>';
                            } else {
                                $chunk .= '<span class="ns">—</span>';
                            }
                            $chunk .= '</td>';
                        }

                        // Subject sub-total
                        $chunk .= '<td class="stc"><div class="sto">' . (int) round($subjObt) . '/' . $subjFull . '</div><div class="stp">' . $subjPct . '%</div><span class="gb ' . $subjGradeClass . '">' . $subjGrade . '</span></td>';

                        // Overall
                        if ($isFirst) {
                            $chunk .= '<td rowspan="' . $rowspan . '" class="ovc"><div class="ovo">' . (int) round($overallObt) . '/' . $overallFull . '</div><div class="ovp">' . $overallPct . '%</div><span class="gb ' . $overallGradeClass . '">' . $overallGrade . '</span><br>';
                            $chunk .= $overallPass ? '<span class="rp">PASS</span>' : '<span class="rf">FAIL</span>';
                            $chunk .= '</td>';
                        }

                        $chunk .= '</tr>';
                        $chunks[] = $chunk;
                        $subjIndex++;
                    }
                } else {
                    $chunks[] = '<tr class="rfi"><td style="text-align:center;color:#888">' . $rollNo . '</td><td><div class="sn">' . $studentName . '</div></td>'
                        . '<td colspan="' . ($examColCount + 2) . '" style="text-align:center;color:#9ca3af;font-style:italic">No subjects enrolled</td></tr>';
                }
            }

            // Footer stats
            $avgPercentage  = $totalMarkCount > 0 ? round($sumPct / $totalMarkCount, 1) : 0;
            $passPercentage = $totalMarkCount > 0 ? round(($passCount / $totalMarkCount) * 100, 1) : 0;

            $footerCells = '';
            foreach ($examDetails as $ed) {
                $examObt = 0; $examFull = 0;
                foreach ($students as $s) {
                    foreach ($allMarks->get($s->id, collect())->where('exam_detail_id', $ed->id) as $me) {
                        if ($me->marks_obtained !== null && !($me->is_absent ?? false)) {
                            $examObt += $me->marks_obtained;
                            $edSetting = $examSettingsByDetail->get($ed->id, collect())
                                ->firstWhere('subject_id', $me->subject_id);
                            $examFull += $edSetting ? ($edSetting->full_mark ?? 0) : 0;
                        }
                    }
                }
                $examAvgPct = $examFull > 0 ? round(($examObt / $examFull) * 100, 1) : 0;
                $footerCells .= '<td style="text-align:center;color:#1e3a8a">avg ' . $examAvgPct . '%</td>';
            }

            $chunks[] = '</tbody><tfoot><tr>'
                . '<td colspan="3" style="text-align:right;font-weight:500">Class overview — ' . $totalStudents . ' students</td>'
                . $footerCells
                . '<td class="fa">avg ' . $avgPercentage . '%</td>'
                . '<td class="fp">Pass ' . $passPercentage . '%<br><span style="font-weight:normal">' . $passCount . '/' . $totalMarkCount . '</span></td>'
                . '</tr></tfoot></table>';

        } else {
            $chunks[] = '<p style="text-align:center;color:#9ca3af;padding:20px;font-style:italic">No data found.</p>';
        }

        $chunks[] = '</body></html>';

        // Generate PDF by writing chunks
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);

        foreach ($chunks as $chunk) {
            $mpdf->WriteHTML($chunk);
        }

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'marks-register.pdf');
    }



    protected function rules()
    {
        return [
            'selected_myclass_id' => 'required|exists:bs04_myclasses,id',
        ];
    }

    public function mount($myclassId = null)
    {
        // Auto-select active session and user's school
        $activeSession = Session::active()->first();
        $this->selected_session_id = $activeSession ? $activeSession->id : null;
        $this->selected_school_id = auth()->user()->school_id;

        $this->loadOptions();

        // Set class from URL parameter
        if ($myclassId && isset($this->myclassOptions[$myclassId])) {
            $this->selected_myclass_id = $myclassId;
        }
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

    public function gradeCssClass($grade)
    {
        switch ($grade) {
            case 'A+': return 'gA-p';
            case 'A':  return 'gA';
            case 'A-': return 'gA-m';
            case 'B':  return 'gB';
            case 'C':  return 'gC';
            case 'D':  return 'gD';
            case 'F':  return 'gF';
            default:   return 'gB';
        }
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
