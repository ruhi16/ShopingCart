<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marks Register</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 2px 0;
            color: #1e3a5f;
        }
        .header .school-name {
            font-size: 14px;
            color: #333;
            margin: 0;
        }
        .header .info {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #c5d5e8;
            padding: 4px 5px;
            vertical-align: top;
        }
        th {
            background-color: #e8f0fe;
            color: #1a3a6c;
            font-weight: 600;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
        }
        th.left-header {
            text-align: left;
        }
        .exam-sub-header {
            font-size: 9px;
            color: #5a7ab5;
            font-weight: normal;
            text-transform: none;
        }
        .student-name {
            font-weight: 600;
            color: #222;
            font-size: 11px;
        }
        .student-class {
            color: #999;
            font-size: 9px;
        }
        .subject-badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 500;
        }
        .subject-normal {
            background: #f0e6f6;
            color: #6b21a8;
        }
        .subject-addl {
            background: #fef3c7;
            color: #92400e;
        }
        .addl-tag {
            font-size: 8px;
            background: #fde68a;
            color: #78350f;
            padding: 0 3px;
            border-radius: 2px;
            margin-left: 2px;
        }
        .mark-cell {
            text-align: center;
        }
        .mark-obtained {
            font-weight: bold;
            color: #1e40af;
        }
        .mark-full {
            color: #9ca3af;
        }
        .absent-badge {
            color: #b91c1c;
            background: #fee2e2;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: 500;
        }
        .no-setting {
            color: #d1d5db;
        }
        .subtotal-cell {
            background: #ecfdf5;
            text-align: center;
        }
        .subtotal-obtained {
            font-weight: 600;
            color: #166534;
        }
        .subtotal-pct {
            color: #15803d;
            font-size: 9px;
        }
        .grade-badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            margin-top: 1px;
        }
        .grade-A-plus { background: #bfdbfe; color: #1e3a8a; }
        .grade-A { background: #dbeafe; color: #1e40af; }
        .grade-A-minus { background: #e0e7ff; color: #3730a3; }
        .grade-B { background: #ccfbf1; color: #115e59; }
        .grade-C { background: #fef9c3; color: #854d0e; }
        .grade-D { background: #ffedd5; color: #9a3412; }
        .grade-F { background: #fee2e2; color: #991b1b; }
        .overall-cell {
            background: #eff6ff;
            text-align: center;
            border-left: 2px solid #93c5fd;
        }
        .overall-obtained {
            font-weight: bold;
            color: #1e3a8a;
            font-size: 12px;
        }
        .overall-pct {
            color: #2563eb;
            font-size: 10px;
        }
        .result-pass {
            display: inline-block;
            background: #bbf7d0;
            color: #166534;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            margin-top: 2px;
        }
        .result-fail {
            display: inline-block;
            background: #fecaca;
            color: #991b1b;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            margin-top: 2px;
        }
        .row-even { background: #f9fafb; }
        .row-first { border-top: 2px solid #d1d5db; }
        tfoot td {
            background: #e8f0fe;
            border-top: 2px solid #93c5fd;
            font-size: 10px;
            color: #1a3a6c;
        }
        .footer-avg {
            background: #ecfdf5;
            color: #166534;
            font-weight: 600;
        }
        .footer-pass {
            background: #dbeafe;
            color: #1e3a8a;
            font-weight: 600;
            border-left: 2px solid #93c5fd;
        }
        .no-data {
            text-align: center;
            color: #9ca3af;
            padding: 20px;
            font-style: italic;
        }
        .exam-col-header {
            min-width: 60px;
        }
    </style>
</head>
<body>

<div class="header">
    @if($school)
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
            <th class="left-header" style="width:110px">Student</th>
            <th class="left-header" style="width:70px">Subject</th>
            @foreach($examDetails as $examDetail)
            <th class="exam-col-header">
                {{ $examDetail->examName->name ?? '—' }}
                <div class="exam-sub-header">
                    {{ $examDetail->semester->name ?? '' }}
                    &middot;
                    {{ $examDetail->examType->name ?? '' }}
                    &middot;
                    {{ $examDetail->examMode->name ?? '' }}
                </div>
            </th>
            @endforeach
            <th style="width:70px;background:#ecfdf5;color:#166534">Sub Total</th>
            <th style="width:80px;background:#dbeafe;color:#1e3a8a;border-left:2px solid #93c5fd">Overall / Result</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $student)
        @php
            $studentSubjects = isset($marksData[$student->id])
                ? array_keys($marksData[$student->id]) : [];
            $subjectCount = count($studentSubjects);
            $rowspan = max(1, $subjectCount);

            // Find the additional subject: the one with the lowest total obtained marks
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
                if ($sid == $additionalSubjectId) continue;
                $sm = $marksData[$student->id][$sid] ?? [];
                foreach ($sm['exam_marks'] ?? [] as $em) {
                    if (isset($em['marks_obtained']) && $em['marks_obtained'] !== null && !$em['is_absent']) {
                        $overallObt += $em['marks_obtained'];
                    }
                }
            }
            $overallPct = $overallFull > 0 ? round(($overallObt / $overallFull) * 100, 1) : 0;
            if ($overallPct >= 80) $overallGrade = 'A+';
            elseif ($overallPct >= 70) $overallGrade = 'A';
            elseif ($overallPct >= 60) $overallGrade = 'A-';
            elseif ($overallPct >= 50) $overallGrade = 'B';
            elseif ($overallPct >= 40) $overallGrade = 'C';
            elseif ($overallPct >= 33) $overallGrade = 'D';
            else $overallGrade = 'F';
            $overallPass = $overallPct >= 40;
        @endphp

        @if($subjectCount > 0)
            @foreach($studentSubjects as $subjIndex => $subjectId)
            @php
                $subjectMarks = $marksData[$student->id][$subjectId] ?? null;
                $subjectInfo = $subjectMarks['subject_info'] ?? null;
                $isFirst = ($subjIndex === 0);
                $isAdditional = ($subjectId == $additionalSubjectId);

                // Subject sub-total (fixed full marks = 100)
                $subjObt = 0;
                $subjFull = 100;
                foreach ($examDetails as $ed) {
                    $md = $subjectMarks['exam_marks'][$ed->id] ?? null;
                    if ($md && isset($md['marks_obtained']) && $md['marks_obtained'] !== null && !$md['is_absent']) {
                        $subjObt += $md['marks_obtained'];
                    }
                }
                $subjPct = $subjFull > 0 ? round(($subjObt / $subjFull) * 100, 1) : 0;
                if ($subjPct >= 80) $subjGrade = 'A+';
                elseif ($subjPct >= 70) $subjGrade = 'A';
                elseif ($subjPct >= 60) $subjGrade = 'A-';
                elseif ($subjPct >= 50) $subjGrade = 'B';
                elseif ($subjPct >= 40) $subjGrade = 'C';
                elseif ($subjPct >= 33) $subjGrade = 'D';
                else $subjGrade = 'F';

                // Grade class helper
                $gradeClass = function($grade) {
                    switch ($grade) {
                        case 'A+': return 'grade-A-plus';
                        case 'A':  return 'grade-A';
                        case 'A-': return 'grade-A-minus';
                        case 'B':  return 'grade-B';
                        case 'C':  return 'grade-C';
                        case 'D':  return 'grade-D';
                        case 'F':  return 'grade-F';
                        default:   return 'grade-B';
                    }
                };
            @endphp

            <tr class="{{ $isFirst ? 'row-first' : '' }} {{ $subjIndex % 2 == 0 ? 'row-even' : '' }}">
                @if($isFirst)
                <td rowspan="{{ $rowspan }}" style="text-align:center;color:#888;font-weight:500;vertical-align:middle">
                    {{ $student->roll_no }}
                </td>
                <td rowspan="{{ $rowspan }}" style="vertical-align:middle">
                    <div class="student-name">{{ $student->studentdb->student_name ?? '—' }}</div>
                    <div class="student-class">
                        {{ $student->currentMyclass->name ?? '' }}
                        {{ $student->currentSection->name ?? '' }}
                    </div>
                </td>
                @endif

                <td>
                    <span class="subject-badge {{ $isAdditional ? 'subject-addl' : 'subject-normal' }}">
                        {{ $subjectInfo->subject->short_name ?? $subjectInfo->subject->name ?? 'Subj '.$subjectId }}
                    </span>
                    @if($isAdditional)
                        <span class="addl-tag">Addl</span>
                    @endif
                </td>

                @foreach($examDetails as $examDetail)
                @php $md = $subjectMarks['exam_marks'][$examDetail->id] ?? null; @endphp
                <td class="mark-cell" style="{{ ($md && isset($md['is_absent']) && $md['is_absent']) ? 'background:#fef2f2;' : '' }}">
                    @if(!$md || !isset($md['exam_setting_id']) || !$md['exam_setting_id'])
                        <span class="no-setting">—</span>
                    @elseif(isset($md['is_absent']) && $md['is_absent'])
                        <span class="absent-badge">AB</span>
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
                    <span class="grade-badge {{ $gradeClass($subjGrade) }}">{{ $subjGrade }}</span>
                </td>

                @if($isFirst)
                <td rowspan="{{ $rowspan }}" class="overall-cell" style="vertical-align:middle">
                    <div class="overall-obtained">{{ (int) round($overallObt) }}/{{ $overallFull }}</div>
                    <div class="overall-pct">{{ $overallPct }}%</div>
                    <span class="grade-badge {{ $gradeClass($overallGrade) }}">{{ $overallGrade }}</span>
                    <br>
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
                <td style="text-align:center;color:#888">{{ $student->roll_no }}</td>
                <td>
                    <div class="student-name">{{ $student->studentdb->student_name ?? '—' }}</div>
                </td>
                <td colspan="{{ count($examDetails) + 2 }}" class="no-data">No subjects enrolled</td>
            </tr>
        @endif
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="3" style="text-align:right;font-weight:500">
                Class overview &mdash; {{ $totalStudents }} students
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
            <td style="text-align:center;color:#1e3a8a">
                avg {{ $examAvgPct }}%
            </td>
            @endforeach
            <td class="footer-avg">
                avg {{ $avgPercentage }}%
            </td>
            <td class="footer-pass">
                Pass {{ $passPercentage }}%<br>
                <span style="font-weight:normal">{{ $passCount }}/{{ $totalMarkCount }}</span>
            </td>
        </tr>
    </tfoot>
</table>

@else
    <p class="no-data">No data found. Check that students are enrolled and exam settings are configured.</p>
@endif

</body>
</html>
