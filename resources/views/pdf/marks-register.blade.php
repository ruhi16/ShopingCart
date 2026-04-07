<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Marks Register</title>
    <style>
        /* Clean, Excel-like style – minimal colors, no heavy effects */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 12px 8px;
            background: white;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 1px solid #aaa;
            padding-bottom: 6px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 2px 0;
            color: #111;
        }

        .header .school-name {
            font-size: 13px;
            font-weight: normal;
            color: #222;
            margin: 2px 0;
        }

        .header .info {
            font-size: 9px;
            color: #333;
            margin-top: 3px;
        }

        /* Table: clean grid, monochrome */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 5px 4px;
            vertical-align: top;
        }

        th {
            background-color: #f1f3f5;
            font-weight: 600;
            text-align: center;
            font-size: 10px;
            color: #111;
            letter-spacing: 0.3px;
        }

        th.left-header {
            text-align: left;
        }

        .exam-sub-header {
            font-size: 8px;
            font-weight: normal;
            color: #2c3e4e;
            margin-top: 2px;
        }

        td {
            background-color: #fff;
        }

        /* Student & subject text */
        .student-name {
            font-weight: 600;
            font-size: 10px;
            color: #000;
        }

        .student-reg {
            font-size: 9px;
            color: #2c3e4e;
        }

        .student-class {
            font-size: 8px;
            color: #4a5568;
            margin-top: 1px;
        }

        .subject-name {
            font-weight: 500;
            font-size: 10px;
        }

        .addl-tag {
            font-size: 8px;
            font-weight: normal;
            color: #6b4c1c;
            margin-left: 3px;
        }

        /* Marks cells – numeric focus */
        .mark-cell {
            text-align: center;
            vertical-align: middle;
        }

        .mark-obtained {
            font-weight: 600;
            color: #000;
        }

        .mark-full {
            color: #3a3f44;
            font-size: 9px;
        }

        .absent-text {
            font-weight: 600;
            color: #b91c1c;
        }

        .no-setting {
            color: #8f9aab;
        }

        /* Subtotal & overall cells (no colors) */
        .subtotal-cell,
        .overall-cell {
            text-align: center;
            vertical-align: middle;
        }

        .subtotal-obtained {
            font-weight: 700;
            color: #000;
        }

        .subtotal-pct,
        .overall-pct {
            font-size: 9px;
            color: #1e2a36;
        }

        .grade-text {
            font-weight: 500;
            font-size: 9px;
            display: inline-block;
            margin-top: 1px;
        }

        .result-pass {
            font-weight: 700;
            color: #1b6b2b;
        }

        .result-fail {
            font-weight: 700;
            color: #b22222;
        }

        .overall-obtained {
            font-weight: 700;
            font-size: 11px;
            color: #000;
        }

        /* Footer summary - no backgrounds */
        tfoot td {
            border-top: 2px solid #888;
            background: #fafafc;
            font-weight: 500;
            padding: 5px 4px;
        }

        .footer-avg,
        .footer-pass {
            font-weight: 600;
            color: #111;
        }

        .no-data {
            text-align: center;
            padding: 18px;
            color: #6c757d;
            font-style: normal;
        }

        /* Row separators: subtle first row border */
        .row-first td {
            border-top: 1px solid #bbb;
        }
    </style>
</head>

<body>

    <div class="header">
        @if(isset($school) && $school)
            <h1>{{ $school->name ?? 'School' }}</h1>
        @endif
        <p class="school-name">Marks Register</p>
        <p class="info">
            Class: <strong>{{ $myclass->name ?? '—' }}</strong>
            &nbsp;|&nbsp; Session: <strong>{{ $session->name ?? '—' }}</strong>
        </p>
    </div>

    @if(isset($students) && count($students) > 0 && isset($examDetails) && is_countable($examDetails) && count($examDetails) > 0)

        <table>
            <thead>
                <tr>
                    <th class="left-header" style="width:30px">Roll</th>
                    <th class="left-header" style="width:120px">Student</th>
                    <th class="left-header" style="width:85px">Subject</th>
                    @foreach($examDetails as $examDetail)
                        <th class="exam-col-header">
                            {{ $examDetail->examName->name ?? '—' }}
                            <div class="exam-sub-header">
                                {{ $examDetail->semester->name ?? '' }}
                                &middot; {{ $examDetail->examType->name ?? '' }}
                                &middot; {{ $examDetail->examMode->name ?? '' }}
                            </div>
                        </th>
                    @endforeach
                    <th style="width:70px">Sub Total</th>
                    <th style="width:85px">Overall / Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    @php
                        $studentSubjects = isset($marksData[$student->id])
                            ? array_keys($marksData[$student->id]) : [];
                        $subjectCount = count($studentSubjects);
                        $rowspan = max(1, $subjectCount);

                        // Find additional subject: the one with lowest total obtained marks
                        $subjectTotals = [];
                        foreach ($studentSubjects as $sid) {
                            $sm = $marksData[$student->id][$sid] ?? [];
                            $obt = 0;
                            foreach ($sm['exam_marks'] ?? [] as $em) {
                                if (isset($em['marks_obtained']) && $em['marks_obtained'] !== null && !$em['is_absent']) {
                                    $obt += $em['marks_obtained'];
                                }
                            }
                            $subjectTotals[$sid] = $obt;
                        }
                        $additionalSubjectId = null;
                        if (!empty($subjectTotals)) {
                            $minVal = min($subjectTotals);
                            foreach ($subjectTotals as $sid => $val) {
                                if ($val == $minVal) {
                                    $additionalSubjectId = $sid;
                                    break;
                                }
                            }
                        }

                        // Overall total: exclude additional subject, fixed out of 500
                        $overallObt = 0;
                        $overallFull = 500;
                        foreach ($studentSubjects as $sid) {
                            if ($sid == $additionalSubjectId)
                                continue;
                            $sm = $marksData[$student->id][$sid] ?? [];
                            foreach ($sm['exam_marks'] ?? [] as $em) {
                                if (isset($em['marks_obtained']) && $em['marks_obtained'] !== null && !$em['is_absent']) {
                                    $overallObt += $em['marks_obtained'];
                                }
                            }
                        }
                        $overallPct = $overallFull > 0 ? round(($overallObt / $overallFull) * 100, 1) : 0;
                        if ($overallPct >= 80)
                            $overallGrade = 'A+';
                        elseif ($overallPct >= 70)
                            $overallGrade = 'A';
                        elseif ($overallPct >= 60)
                            $overallGrade = 'A-';
                        elseif ($overallPct >= 50)
                            $overallGrade = 'B';
                        elseif ($overallPct >= 40)
                            $overallGrade = 'C';
                        elseif ($overallPct >= 33)
                            $overallGrade = 'D';
                        else
                            $overallGrade = 'F';
                        $overallPass = $overallPct >= 40;
                    @endphp

                    @if($subjectCount > 0)
                        @foreach($studentSubjects as $subjIndex => $subjectId)
                            @php
                                $subjectMarks = $marksData[$student->id][$subjectId] ?? null;
                                $subjectInfo = $subjectMarks['subject_info'] ?? null;
                                $isFirst = ($subjIndex === 0);
                                $isAdditional = ($subjectId == $additionalSubjectId);

                                // Subject sub-total (full marks = 100 per original logic)
                                $subjObt = 0;
                                $subjFull = 100;
                                foreach ($examDetails as $ed) {
                                    $md = $subjectMarks['exam_marks'][$ed->id] ?? null;
                                    if ($md && isset($md['marks_obtained']) && $md['marks_obtained'] !== null && !$md['is_absent']) {
                                        $subjObt += $md['marks_obtained'];
                                    }
                                }
                                $subjPct = $subjFull > 0 ? round(($subjObt / $subjFull) * 100, 1) : 0;
                                if ($subjPct >= 80)
                                    $subjGrade = 'A+';
                                elseif ($subjPct >= 70)
                                    $subjGrade = 'A';
                                elseif ($subjPct >= 60)
                                    $subjGrade = 'A-';
                                elseif ($subjPct >= 50)
                                    $subjGrade = 'B';
                                elseif ($subjPct >= 40)
                                    $subjGrade = 'C';
                                elseif ($subjPct >= 33)
                                    $subjGrade = 'D';
                                else
                                    $subjGrade = 'F';
                            @endphp

                            <tr class="{{ $isFirst ? 'row-first' : '' }}">
                                @if($isFirst)
                                    <td style="text-align:center; vertical-align:middle; font-weight:500;">
                                        {{ $student->roll_no }}
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <div class="student-name">{{ $student->studentdb->student_name ?? '—' }}</div>
                                        <div class="student-reg">{{ $student->studentdb->student_reg_no ?? 'No Reg No' }}</div>
                                        <div class="student-class">
                                            {{ $student->currentMyclass->name ?? '' }}
                                            {{ $student->currentSection->name ?? '' }}
                                        </div>
                                    </td>
                                @endif

                                <td>
                                    <span class="subject-name">
                                        {{ $subjectInfo->subject->short_name ?? $subjectInfo->subject->name ?? 'Subj ' . $subjectId }}
                                    </span>
                                    @if($isAdditional)
                                        <span class="addl-tag">(Addl)</span>
                                    @endif
                                </td>

                                @foreach($examDetails as $examDetail)
                                    @php $md = $subjectMarks['exam_marks'][$examDetail->id] ?? null; @endphp
                                    <td class="mark-cell" style="{{ ($md && isset($md['is_absent']) && $md['is_absent']) ? '' : '' }}">
                                        @if(!$md || !isset($md['exam_setting_id']) || !$md['exam_setting_id'])
                                            <span class="no-setting">—</span>
                                        @elseif(isset($md['is_absent']) && $md['is_absent'])
                                            <span class="absent-text">AB</span>
                                        @elseif(isset($md['marks_obtained']) && $md['marks_obtained'] !== null)
                                            <span class="mark-obtained">{{ (int) round($md['marks_obtained']) }}</span>
                                            <span class="mark-full">/{{ $md['full_mark'] }}</span>
                                        @else
                                            <span class="no-setting">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="subtotal-cell">
                                    <div class="subtotal-obtained">{{ (int) round($subjObt) }}/{{ $subjFull }}</div>
                                    <div class="subtotal-pct">{{ $subjPct }}%</div>
                                    <span class="grade-text">{{ $subjGrade }}</span>
                                </td>

                                @if($isFirst)
                                    <td class="overall-cell" rowspan="{{ $rowspan }}" style="vertical-align:middle;">
                                        <div class="overall-obtained">{{ (int) round($overallObt) }}/{{ $overallFull }}</div>
                                        <div class="overall-pct">{{ $overallPct }}%</div>
                                        <span class="grade-text">{{ $overallGrade }}</span><br>
                                        @if($overallPass)
                                            <span class="result-pass">PASS</span>
                                        @else
                                            <span class="result-fail">FAIL</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr class="row-first">
                            <td style="text-align:center;">{{ $student->roll_no }}</td>
                            <td colspan="2">
                                <div class="student-name">{{ $student->studentdb->student_name ?? '—' }}</div>
                                <div class="student-reg">{{ $student->studentdb->student_reg_no ?? 'No Reg No' }}</div>
                            </td>
                            <td colspan="{{ count($examDetails) + 2 }}" class="no-data">No subjects enrolled</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:500;">
                        Class overview — {{ $totalStudents }} students
                    </td>
                    @foreach($examDetails as $examDetail)
                        @php
                            $examObt = 0;
                            $examFull = 0;
                            $examCount = 0;
                            foreach ($students as $student) {
                                foreach ($marksData[$student->id] ?? [] as $sm) {
                                    $em = $sm['exam_marks'][$examDetail->id] ?? null;
                                    if ($em && isset($em['marks_obtained']) && $em['marks_obtained'] !== null && !$em['is_absent']) {
                                        $examObt += $em['marks_obtained'];
                                        $examFull += $em['full_mark'];
                                        $examCount++;
                                    }
                                }
                            }
                            $examAvgPct = $examFull > 0 ? round(($examObt / $examFull) * 100, 1) : 0;
                        @endphp
                        <td style="text-align:center;">
                            avg {{ $examAvgPct }}%
                        </td>
                    @endforeach
                    <td class="footer-avg" style="text-align:center;">
                        avg {{ $avgPercentage }}%
                    </td>
                    <td class="footer-pass" style="text-align:center;">
                        Pass {{ $passPercentage }}%<br>
                        <span style="font-weight:normal;">{{ $passCount }}/{{ $totalMarkCount }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>

    @else
        <p class="no-data">No data found. Check that students are enrolled and exam settings are configured.</p>
    @endif

</body>

</html>