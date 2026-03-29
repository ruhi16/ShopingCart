<div class="p-6">
    {{-- Header --}}
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Marks Register - Class Wise</h2>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Session *</label>
                <select wire:model="selected_session_id" class="w-full rounded border-gray-300">
                    <option value="">-- Select Session --</option>
                    @foreach($sessionOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">School *</label>
                <select wire:model="selected_school_id" class="w-full rounded border-gray-300">
                    <option value="">-- Select School --</option>
                    @foreach($schoolOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                <select wire:model="selected_myclass_id" class="w-full rounded border-gray-300">
                    <option value="">-- Select Class --</option>
                    @foreach($myclassOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                <select wire:model="selected_semester_id" class="w-full rounded border-gray-300">
                    <option value="">-- Select Semester --</option>
                    @foreach($semesterOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        {{-- Search Box --}}
        <div class="mt-4">
            <div class="flex items-center space-x-2">
                <input 
                    type="text" 
                    wire:model.debounce.300ms="search" 
                    placeholder="Search by student name..." 
                    class="flex-1 rounded border-gray-300"
                />
                <button 
                    wire:click="resetFilters" 
                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600"
                >
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    {{-- Marks Register Table with Sub-rows --}}
    @if(count($students) > 0 && count($examDetails) > 0)
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        {{-- Student Info Columns (5 columns) --}}
                        <th rowspan="2" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border-r">Roll No</th>
                        <th rowspan="2" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border-r">Student Name</th>
                        {{-- <th rowspan="2" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border-r">Class</th> --}}
                        {{-- <th rowspan="2" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border-r">Section</th> --}}
                        <th rowspan="2" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border-r">Subjects</th>
                        
                        {{-- Exam Detail Headers --}}
                        @foreach($examDetails as $index => $examDetail)
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase border-t border-l border-r bg-blue-50 {{ $loop->first ? '' : 'border-l-2' }}">
                                <div class="font-bold">{{ $examDetail->examName->name ?? 'N/A' }}</div>
                                <div class="text-xs">{{ $examDetail->examType->name ?? 'N/A' }}</div>
                                <div class="text-xs">{{ $examDetail->examPart->name ?? 'N/A' }} • {{ $examDetail->examMode->name ?? 'N/A' }}</div>
                            </th>
                        @endforeach
                        
                        {{-- Total & Grade Column --}}
                        <th rowspan="2" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase border-t border-b border-r bg-green-50">Subject Total / Grade</th>
                    </tr>
                    
                    <tr>
                        {{-- Empty row for alignment --}}
                        @foreach($examDetails as $examDetail)
                            <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase border border-gray-300 bg-gray-100">
                                Marks Obtained
                            </th>
                        @endforeach
                    </tr>
                </thead>
                
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                        @php
                            // Get subjects for this student from marksData
                            $studentSubjects = isset($this->marksData[$student->id]) ? array_keys($this->marksData[$student->id]) : [];
                            $subjectCount = count($studentSubjects);
                            $rowspan = max(1, $subjectCount);
                        @endphp
                        
                        {{-- First Subject Row (with student info) --}}
                        @if($subjectCount > 0)
                            @foreach($studentSubjects as $subjIndex => $subjectId)
                                @php
                                    $subjectMarks = $this->marksData[$student->id][$subjectId] ?? null;
                                    $subjectInfo = $subjectMarks['subject_info'] ?? null;
                                    $isFirstRow = ($subjIndex === 0);
                                @endphp
                                
                                <tr class="{{ $isFirstRow ? 'bg-blue-50 hover:bg-blue-100' : 'hover:bg-gray-50 border-t border-gray-200' }}">
                                    @if($isFirstRow)
                                        {{-- Student Info (only in first subject row) --}}
                                        <td rowspan="{{ $rowspan }}" class="px-3 py-2 text-sm font-bold text-gray-900 border-r align-top">{{ $student->roll_no }}</td>
                                        <td rowspan="{{ $rowspan }}" class="px-3 py-2 text-sm font-bold text-gray-900 border-r align-top">
                                            {{ $student->studentdb->student_name ?? 'N/A' }}<br/>
                                            {{ $student->currentMyclass->name ?? 'N/A' }}
                                            {{ $student->currentSection->name ?? 'N/A' }} <br/>
                                            {{ $student->currentSemester->name ?? 'N/A' }} Semester

                                        </td>

                                        {{-- <td rowspan="{{ $rowspan }}" class="px-3 py-2 text-sm text-gray-900 border-r align-top">{{ $student->currentMyclass->name ?? 'N/A' }}</td>
                                        <td rowspan="{{ $rowspan }}" class="px-3 py-2 text-sm text-gray-900 border-r align-top">{{ $student->currentSection->name ?? 'N/A' }}</td>
                                        <td rowspan="{{ $rowspan }}" class="px-3 py-2 text-sm text-gray-900 border-r align-top">{{ $student->currentSemester->name ?? 'N/A' }}</td> --}}
                                    @endif
                                    
                                    {{-- Subject Name --}}
                                    <td class="px-3 py-2 text-sm font-semibold text-gray-800 border-r {{ !$isFirstRow ? 'bg-gray-50' : '' }}">
                                        <div class="flex items-center">
                                            <span class="font-bold">{{ $subjectInfo->subject->short_name ?? $subjectInfo->subject->name ?? 'Subject ' . $subjectId }}</span>
                                            {{-- @if($isFirstRow)
                                                <span class="ml-2 text-xs text-gray-500">({{ $subjectCount }} subjects total)</span>
                                            @endif --}}
                                        </div>
                                    </td>
                                    
                                    {{-- Marks for each exam --}}
                                    @foreach($examDetails as $examDetail)
                                        @php
                                            $markData = $subjectMarks['exam_marks'][$examDetail->id] ?? null;
                                        @endphp
                                        <td class="px-2 py-2 text-center text-sm border border-gray-300 {{ $markData && $markData['is_absent'] ? 'bg-red-50' : '' }} {{ $markData && !$markData['is_absent'] ? 'bg-green-50' : '' }}">
                                            
                                            @if($markData && isset($markData['exam_setting_id']) && $markData['exam_setting_id'])
                                                @if($markData['is_absent'])
                                                    <span class="text-red-600 font-bold text-xs">ABSENT</span>
                                                @elseif(isset($markData['marks_obtained']) && $markData['marks_obtained'] !== null)
                                                    <div class="text-xs font-bold text-blue-800">{{ number_format($markData['marks_obtained'], 2) }}</div>
                                                    <div class="text-xs text-gray-600">/{{ $markData['full_mark'] }}</div>
                                                    <div class="text-xs font-semibold {{ $markData['grade'] == 'F' ? 'text-red-600' : 'text-green-600' }}">{{ $markData['grade'] }}</div>
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            @else
                                                <span class="text-gray-300 text-xs">N/A</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                    {{-- Subject Total --}}
                                    @php
                                        $subjTotal = 0;
                                        $subjFull = 0;
                                        foreach($examDetails as $examDetail) {
                                            $md = $subjectMarks['exam_marks'][$examDetail->id] ?? null;
                                            if($md && isset($md['marks_obtained']) && $md['marks_obtained'] !== null && !$md['is_absent']) {
                                                $subjTotal += $md['marks_obtained'];
                                                $subjFull += $md['full_mark'];
                                            }
                                        }
                                        $subjPct = $subjFull > 0 ? round(($subjTotal / $subjFull) * 100, 2) : 0;
                                        $subjGrade = $this->calculateGrade($subjPct);
                                    @endphp
                                    <td class="px-3 py-2 text-center text-sm font-bold border border-gray-300 bg-green-50 align-top" {{ $isFirstRow ? 'rowspan="' . $rowspan . '"' : '' }}>
                                        @if($isFirstRow)
                                            <div class="text-xs text-gray-600 mb-1">Per Subject Average:</div>
                                        @endif
                                        <div>{{ number_format($subjTotal, 2) }}/{{ $subjFull }}</div>
                                        <div class="text-xs">{{ $subjPct }}%</div>
                                        <div class="text-sm font-bold {{ $subjGrade == 'F' ? 'text-red-600' : 'text-green-600' }}">{{ $subjGrade }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            {{-- No subjects for this student --}}
                            <tr>
                                <td class="px-3 py-2 text-sm font-bold text-gray-900 border-r">{{ $student->roll_no }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900 border-r">{{ $student->studentdb->student_name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900 border-r">{{ $student->currentMyclass->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900 border-r">{{ $student->currentSection->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900 border-r">{{ $student->currentSemester->name ?? 'N/A' }}</td>
                                <td colspan="{{ count($examDetails) + 1 }}" class="px-3 py-2 text-center text-sm text-gray-400 border border-gray-300">
                                    No subjects opted
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                
                {{-- Summary Footer --}}
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="5" class="px-3 py-3 text-right text-sm text-gray-700 border-r border-b">Overall Class Statistics:</td>
                        @php
                            $totalStudents = count($students);
                            $avgPercentage = 0;
                            $passCount = 0;
                            foreach($students as $student) {
                                foreach($this->marksData[$student->id] ?? [] as $subjectId => $subjectMarks) {
                                    foreach($subjectMarks['exam_marks'] ?? [] as $examMark) {
                                        if(isset($examMark['percentage'])) {
                                            $avgPercentage += $examMark['percentage'];
                                            if($examMark['percentage'] >= 40) $passCount++;
                                        }
                                    }
                                }
                            }
                            $totalMarks = 0;
                            foreach($students as $student) {
                                foreach($this->marksData[$student->id] ?? [] as $subjectId => $subjectMarks) {
                                    foreach($subjectMarks['exam_marks'] ?? [] as $examMark) {
                                        if(isset($examMark['marks_obtained']) && $examMark['marks_obtained'] !== null) {
                                            $totalMarks++;
                                        }
                                    }
                                }
                            }
                            $avgPercentage = $totalMarks > 0 ? round($avgPercentage / $totalMarks, 2) : 0;
                            $passPercentage = $totalMarks > 0 ? round(($passCount / $totalMarks) * 100, 2) : 0;
                        @endphp
                        @foreach($examDetails as $examDetail)
                            <td class="px-2 py-2 text-center text-xs border border-gray-300 bg-gray-200">
                                <div class="text-xs">Avg: {{ $avgPercentage }}%</div>
                            </td>
                        @endforeach
                        <td class="px-3 py-2 text-center text-sm border border-gray-300 bg-green-100">
                            <div class="text-xs">Pass Rate: {{ $passPercentage }}%</div>
                            <div class="text-xs">{{ $passCount }}/{{ $totalMarks }}</div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        {{-- Summary Statistics Cards --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600">Total Students</div>
                <div class="text-2xl font-bold text-blue-800">{{ count($students) }}</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600">Total Exams</div>
                <div class="text-2xl font-bold text-purple-800">{{ count($examDetails) }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600">Class Average</div>
                <div class="text-2xl font-bold text-green-800">{{ $avgPercentage }}%</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600">Pass Percentage</div>
                <div class="text-2xl font-bold text-yellow-800">{{ $passPercentage }}%</div>
            </div>
        </div>
    @elseif($selected_session_id && $selected_school_id && $selected_myclass_id && $selected_semester_id)
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p class="text-gray-500">No data found for the selected filters.</p>
            <p class="text-sm text-gray-400 mt-2">Make sure students are enrolled in this class/semester and have opted for subjects.</p>
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p class="text-gray-500">Please select Session, School, Class, and Semester to view the marks register.</p>
        </div>
    @endif
</div>
