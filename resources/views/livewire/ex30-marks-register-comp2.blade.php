<div class="p-4">

    {{-- Filters --}}
    <div class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Class</label>
            <select wire:model="selected_myclass_id"
                    class="text-sm rounded border-gray-300 focus:border-blue-400 focus:ring-1 focus:ring-blue-300">
                <option value="">— Select Class —</option>
                @foreach($myclassOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        @if($selected_myclass_id)
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Search</label>
            <input type="text"
                   wire:model.debounce.300ms="search"
                   placeholder="Student name…"
                   class="w-full text-sm rounded border-gray-300 focus:border-blue-400 focus:ring-1 focus:ring-blue-300" />
        </div>
        @endif

        <button wire:click="resetFilters"
                class="text-xs px-3 py-2 rounded border border-gray-300 text-gray-500 hover:bg-gray-100 transition">
            Reset
        </button>
    </div>

    {{-- Main Table --}}
    @if(isset($students) && count($students) > 0 && isset($examDetails) && is_countable($examDetails) && count($examDetails) > 0)

    {{-- Pre-compute footer stats --}}
    @php
        $totalStudents  = count($students);
        $totalMarkCount = 0;
        $sumPct         = 0;
        $passCount      = 0;

        foreach ($students as $student) {
            foreach ($this->marksData[$student->id] ?? [] as $subjectMarks) {
                foreach ($subjectMarks['exam_marks'] ?? [] as $em) {
                    if (isset($em['marks_obtained']) && $em['marks_obtained'] !== null && !$em['is_absent']) {
                        $totalMarkCount++;
                        $pct = $em['full_mark'] > 0
                            ? round(($em['marks_obtained'] / $em['full_mark']) * 100, 1)
                            : 0;
                        $sumPct += $pct;
                        if ($pct >= 40) $passCount++;
                    }
                }
            }
        }

        $avgPercentage  = $totalMarkCount > 0 ? round($sumPct / $totalMarkCount, 1) : 0;
        $passPercentage = $totalMarkCount > 0 ? round(($passCount / $totalMarkCount) * 100, 1) : 0;
    @endphp

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full border-collapse text-xs">

            {{-- THEAD --}}
            <thead>
                <tr class="bg-blue-50">
                    <th class="px-3 py-2 text-left font-medium text-blue-700 uppercase tracking-wide border-b border-r border-blue-200 w-10">
                        Roll
                    </th>
                    <th class="px-3 py-2 text-left font-medium text-blue-700 uppercase tracking-wide border-b border-r border-blue-200 min-w-[130px]">
                        Student
                    </th>
                    <th class="px-3 py-2 text-left font-medium text-blue-700 uppercase tracking-wide border-b border-r border-blue-200 min-w-[80px]">
                        Subject
                    </th>

                    @foreach($examDetails as $examDetail)
                    <th class="px-3 py-1 text-center border-b border-r border-blue-200 bg-blue-50 min-w-[80px]">
                        <div class="font-semibold text-blue-800 text-xs">
                            {{ $examDetail->examName->name ?? '—' }}
                        </div>
                        <div class="text-blue-500 font-normal" style="font-size:18px">
                            {{ $examDetail->semester->name ?? '' }}
                            &nbsp;·&nbsp;
                            {{ $examDetail->examType->name ?? '' }}
                            &nbsp;·&nbsp;
                            {{ $examDetail->examMode->name ?? '' }}
                        </div>
                    </th>
                    @endforeach

                    <th class="px-3 py-2 text-center font-medium text-green-700 uppercase tracking-wide border-b border-r border-green-200 bg-green-50 min-w-[80px]">
                        Sub Total
                    </th>
                    <th class="px-3 py-2 text-center font-medium text-blue-800 uppercase tracking-wide border-b border-blue-300 bg-blue-100 min-w-[90px]"
                        style="border-left:2px solid #93c5fd">
                        Overall&nbsp;/&nbsp;Result
                    </th>
                </tr>
            </thead>

            {{-- TBODY --}}
            <tbody class="divide-y divide-gray-100">
                @foreach($students as $student)
                @php
                    $studentSubjects = isset($this->marksData[$student->id])
                        ? array_keys($this->marksData[$student->id]) : [];
                    $subjectCount = count($studentSubjects);
                    $rowspan      = max(1, $subjectCount);

                    // Find the additional subject: the one with the lowest total obtained marks
                    $subjectTotals = [];
                    foreach ($studentSubjects as $sid) {
                        $sm  = $this->marksData[$student->id][$sid] ?? [];
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
                    $overallObt  = 0;
                    $overallFull = 500;
                    foreach ($studentSubjects as $sid) {
                        if ($sid == $additionalSubjectId) continue;
                        $sm = $this->marksData[$student->id][$sid] ?? [];
                        foreach ($sm['exam_marks'] ?? [] as $em) {
                            if (isset($em['marks_obtained']) && $em['marks_obtained'] !== null && !$em['is_absent']) {
                                $overallObt += $em['marks_obtained'];
                            }
                        }
                    }
                    $overallPct   = $overallFull > 0 ? round(($overallObt / $overallFull) * 100, 1) : 0;
                    $overallGrade = $this->calculateGrade($overallPct);
                    $overallPass  = $overallPct >= 40;
                @endphp

                @if($subjectCount > 0)
                    @foreach($studentSubjects as $subjIndex => $subjectId)
                    @php
                        $subjectMarks = $this->marksData[$student->id][$subjectId] ?? null;
                        $subjectInfo  = $subjectMarks['subject_info'] ?? null;
                        $isFirst      = ($subjIndex === 0);
                        $isAdditional = ($subjectId == $additionalSubjectId);

                        // Subject sub-total (fixed full marks = 100)
                        $subjObt  = 0;
                        $subjFull = 100;
                        foreach ($examDetails as $ed) {
                            $md = $subjectMarks['exam_marks'][$ed->id] ?? null;
                            if ($md && isset($md['marks_obtained']) && $md['marks_obtained'] !== null && !$md['is_absent']) {
                                $subjObt += $md['marks_obtained'];
                            }
                        }
                        $subjPct   = $subjFull > 0 ? round(($subjObt / $subjFull) * 100, 1) : 0;
                        $subjGrade = $this->calculateGrade($subjPct);

                        // Grade badge class — switch for PHP 7.4 compatibility
                        switch ($subjGrade) {
                            case 'A+': $subjGradeClass = 'bg-blue-200 text-blue-900'; break;
                            case 'A':  $subjGradeClass = 'bg-blue-100 text-blue-800'; break;
                            case 'B+': $subjGradeClass = 'bg-teal-100 text-teal-800'; break;
                            case 'B':  $subjGradeClass = 'bg-teal-50 text-teal-700';  break;
                            case 'C':  $subjGradeClass = 'bg-yellow-100 text-yellow-800'; break;
                            case 'D':  $subjGradeClass = 'bg-orange-100 text-orange-700'; break;
                            case 'F':  $subjGradeClass = 'bg-red-100 text-red-700';   break;
                            default:   $subjGradeClass = 'bg-gray-100 text-gray-600'; break;
                        }

                        // Overall grade badge class
                        switch ($overallGrade) {
                            case 'A+': $overallGradeClass = 'bg-blue-200 text-blue-900'; break;
                            case 'A':  $overallGradeClass = 'bg-blue-100 text-blue-800'; break;
                            case 'B+': $overallGradeClass = 'bg-teal-100 text-teal-800'; break;
                            case 'B':  $overallGradeClass = 'bg-teal-50 text-teal-700';  break;
                            case 'C':  $overallGradeClass = 'bg-yellow-100 text-yellow-800'; break;
                            case 'D':  $overallGradeClass = 'bg-orange-100 text-orange-700'; break;
                            case 'F':  $overallGradeClass = 'bg-red-100 text-red-700';   break;
                            default:   $overallGradeClass = 'bg-gray-100 text-gray-600'; break;
                        }
                    @endphp

                    <tr class="{{ $isFirst ? 'bg-gray-50 border-t-2 border-gray-300' : 'bg-white' }} hover:bg-blue-50/40 transition">

                        @if($isFirst)
                        <td rowspan="{{ $rowspan }}"
                            class="px-3 py-2 text-gray-400 font-medium border-r border-gray-200 align-top"
                            style="font-size:11px;padding-top:10px">
                            {{ $student->roll_no }}
                        </td>
                        <td rowspan="{{ $rowspan }}"
                            class="px-3 py-2 border-r border-gray-200 align-top"
                            style="padding-top:10px">
                            <div class="font-medium text-gray-800" style="font-size:13px">
                                {{ $student->studentdb->student_name ?? '—' }}
                            </div>
                            <div class="text-gray-400" style="font-size:18px">
                                Class: {{ $student->currentMyclass->name ?? '' }}
                                {{-- {{ $student->currentSection->name ?? '' }} --}}
                            </div>
                            <div class="text-gray-400" style="font-size:18px">
                                Reg No: {{ $student->studentdb->board_reg_no ?? '' }}
                            </div>
                        </td>
                        @endif

                        {{-- Subject cell --}}
                        <td class="px-3 py-2 border-r border-gray-200 {{ $isAdditional ? 'bg-amber-50' : 'bg-purple-50' }}">
                            <span class="inline-block rounded px-2 py-0.5 font-medium
                                         {{ $isAdditional ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800' }}"
                                  style="font-size:16px; font-weight:500">
                                {{ $subjectInfo->subject->short_name
                                   ?? $subjectInfo->subject->name
                                   ?? 'Subj '.$subjectId }}
                            </span>
                            @if($isAdditional)
                                <span class="inline-block rounded px-1 ml-1 font-medium bg-amber-200 text-amber-900"
                                      style="font-size:9px">Addl</span>
                            @endif
                        </td>

                        {{-- Single marks column per exam: integer_obt/max --}}
                        @foreach($examDetails as $examDetail)
                        @php $md = $subjectMarks['exam_marks'][$examDetail->id] ?? null; @endphp
                        <td class="px-2 py-2 text-center border-r border-gray-100
                                   {{ ($md && isset($md['is_absent']) && $md['is_absent']) ? 'bg-red-50' : '' }}">
                            @if(!$md || !isset($md['exam_setting_id']) || !$md['exam_setting_id'])
                                <span class="text-gray-300">—</span>
                            @elseif(isset($md['is_absent']) && $md['is_absent'])
                                <span class="inline-block text-red-700 bg-red-100 rounded px-1"
                                      style="font-size:18px;font-weight:500">AB</span>
                            @elseif(isset($md['marks_obtained']) && $md['marks_obtained'] !== null)
                                <span class="font-bold text-blue-800" style="font-size:18px">
                                    {{ (int) round($md['marks_obtained']) }}
                                </span>
                                <span class="text-gray-400">/{{ $md['full_mark'] }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        @endforeach

                        {{-- Subject sub-total --}}
                        <td class="px-3 py-2 text-center bg-green-50 border-r border-green-100">
                            <div class="font-medium text-green-800" style="font-size:18px">{{ (int) round($subjObt) }}/{{ $subjFull }}</div>
                            <div class="text-green-600" style="font-size:12px">{{ $subjPct }}%</div>
                            <span class="inline-block rounded px-1.5 font-medium {{ $subjGradeClass }}"
                                  style="font-size:12px;margin-top:2px">
                                {{ $subjGrade }}
                            </span>
                        </td>

                        {{-- Overall / Result — rowspan on first subject row only --}}
                        @if($isFirst)
                        <td rowspan="{{ $rowspan }}"
                            class="px-3 py-2 text-center align-middle bg-blue-50"
                            style="border-left:2px solid #93c5fd">
                            <div class="font-bold text-blue-900" style="font-size:18px">
                                {{ (int) round($overallObt) }}/{{ $overallFull }}
                            </div>
                            <div class="text-blue-600" style="font-size:18px">{{ $overallPct }}%</div>
                            <span class="inline-block rounded px-2 font-medium {{ $overallGradeClass }} mt-1"
                                  style="font-size:18px">
                                {{ $overallGrade }}
                            </span>
                            <div class="mt-1">
                                @if($overallPass)
                                    <span class="inline-block rounded px-2 py-0.5 font-medium bg-green-200 text-green-900"
                                          style="font-size:11px">PASS</span>
                                @else
                                    <span class="inline-block rounded px-2 py-0.5 font-medium bg-red-200 text-red-900"
                                          style="font-size:11px">FAIL</span>
                                @endif
                            </div>
                        </td>
                        @endif

                    </tr>
                    @endforeach

                @else
                {{-- No subjects enrolled --}}
                <tr class="bg-gray-50">
                    <td class="px-3 py-2 text-gray-400 font-medium border-r border-gray-200"
                        style="font-size:11px">{{ $student->roll_no }}</td>
                    <td class="px-3 py-2 border-r border-gray-200">
                        <div class="font-medium text-gray-800" style="font-size:13px">
                            {{ $student->studentdb->student_name ?? '—' }}
                        </div>
                    </td>
                    <td colspan="{{ count($examDetails) + 2 }}"
                        class="px-3 py-2 text-center text-gray-400 italic">
                        No subjects enrolled
                    </td>
                </tr>
                @endif

                @endforeach
            </tbody>

            {{-- TFOOT --}}
            <tfoot>
                <tr class="bg-blue-100 border-t-2 border-blue-300">
                    <td colspan="3"
                        class="px-3 py-2 text-right font-medium text-blue-700 border-r border-blue-200"
                        style="font-size:11px">
                        Class overview &mdash; {{ $totalStudents }} students
                    </td>

                    @foreach($examDetails as $examDetail)
                    @php
                        $examObt   = 0;
                        $examFull  = 0;
                        $examCount = 0;
                        foreach ($students as $student) {
                            foreach ($this->marksData[$student->id] ?? [] as $sm) {
                                $em = $sm['exam_marks'][$examDetail->id] ?? null;
                                if ($em && isset($em['marks_obtained']) && $em['marks_obtained'] !== null && !$em['is_absent']) {
                                    $examObt  += $em['marks_obtained'];
                                    $examFull += $em['full_mark'];
                                    $examCount++;
                                }
                            }
                        }
                        $examAvgPct = $examFull > 0 ? round(($examObt / $examFull) * 100, 1) : 0;
                    @endphp
                    <td class="px-2 py-2 text-center text-blue-800 border-r border-blue-200"
                        style="font-size:11px">
                        avg {{ $examAvgPct }}%
                    </td>
                    @endforeach

                    <td class="px-3 py-2 text-center font-medium text-green-800 bg-green-100 border-r border-green-200"
                        style="font-size:11px">
                        avg {{ $avgPercentage }}%
                    </td>
                    <td class="px-3 py-2 text-center font-medium text-blue-900 bg-blue-200"
                        style="font-size:11px;border-left:2px solid #93c5fd">
                        Pass {{ $passPercentage }}%<br>
                        <span class="font-normal text-blue-700">{{ $passCount }}/{{ $totalMarkCount }}</span>
                    </td>
                </tr>
            </tfoot>

        </table>
    </div>

    {{-- Summary stat cards --}}
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-lg bg-blue-50 border border-blue-100 px-4 py-3">
            <div class="text-xs text-blue-500 uppercase tracking-wide mb-1">Students</div>
            <div class="text-2xl font-semibold text-blue-800">{{ $totalStudents }}</div>
        </div>
        <div class="rounded-lg bg-purple-50 border border-purple-100 px-4 py-3">
            <div class="text-xs text-purple-500 uppercase tracking-wide mb-1">Exams</div>
            <div class="text-2xl font-semibold text-purple-800">{{ count($examDetails) }}</div>
        </div>
        <div class="rounded-lg bg-green-50 border border-green-100 px-4 py-3">
            <div class="text-xs text-green-600 uppercase tracking-wide mb-1">Class avg</div>
            <div class="text-2xl font-semibold text-green-800">{{ $avgPercentage }}%</div>
        </div>
        <div class="rounded-lg bg-amber-50 border border-amber-100 px-4 py-3">
            <div class="text-xs text-amber-600 uppercase tracking-wide mb-1">Pass rate</div>
            <div class="text-2xl font-semibold text-amber-800">{{ $passPercentage }}%</div>
        </div>
    </div>

    @elseif($selected_myclass_id)
    <div class="py-10 text-center text-gray-400 text-sm">
        No data found. Check that students are enrolled and exam settings are configured.
    </div>
    @else
    <div class="py-10 text-center text-gray-400 text-sm">
        Select a class above to view the marks register.
    </div>
    @endif

</div>