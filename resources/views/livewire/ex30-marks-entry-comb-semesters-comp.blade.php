<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Marks Entry - Combined Semesters</h2>
            <p class="text-sm text-gray-600 mt-1">Enter marks for all semesters at once</p>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        {{-- Selection Form --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Select Options</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Class Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                    <select 
                        wire:model="selectedClassId" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    >
                        <option value="">Select Class</option>
                        @foreach($myclasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedClassId') 
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Subject Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select 
                        wire:model="selectedSubjectId" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    >
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->subject_code }})</option>
                        @endforeach
                    </select>
                    @error('selectedSubjectId') 
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Info about auto-selected values --}}
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-800">
                <p>
                    <strong>✓ Auto-selected:</strong> 
                    Session ID: {{ $sessionId ?? 'Not set' }} | 
                    School ID: {{ $schoolId ?? 'Not set' }} 
                    <span class="text-gray-600">(from your account)</span>
                </p>
            </div>
        </div>

        {{-- Marks Entry Table --}}
        @if(count($students) > 0 && count($semesters) > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Marks Entry Form</h3>
                    <button 
                        wire:click="saveMarks"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Save All Marks
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">
                                    Roll No
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-16 bg-gray-50">
                                    Board Reg No
                                </th>
                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                    Action
                                </th>
                                {{-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student Name
                                </th> --}}
                                @php
                                    // Ensure semesters are unique by ID
                                    $uniqueSemesters = $semesters->unique('id')->values();
                                @endphp
                                @foreach($uniqueSemesters as $semester)
                                    @php
                                        $headerSemesterId = is_object($semester) ? $semester->id : (is_array($semester) ? ($semester['id'] ?? null) : null);
                                        $headerSemesterName = is_object($semester) ? $semester->name : ($semester['name'] ?? 'Unknown');
                                        $headerDetailCount = $headerSemesterId ? count($examDetailsBySemester[$headerSemesterId] ?? []) : 0;
                                    @endphp
                                    <th colspan="{{ $headerDetailCount }}" 
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l-2 border-gray-300">
                                        {{ $headerSemesterName }}
                                    </th>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50"></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-16 bg-gray-50"></th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"></th>
                                {{-- <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th> --}}
                                @foreach($uniqueSemesters as $semester)
                                    @php
                                        $headerSemesterId = is_object($semester) ? $semester->id : (is_array($semester) ? ($semester['id'] ?? null) : null);
                                        $headerExamDetails = $headerSemesterId ? ($examDetailsBySemester[$headerSemesterId] ?? []) : [];
                                        // Ensure exam details are unique by ID
                                        if (is_object($headerExamDetails) || is_array($headerExamDetails)) {
                                            $headerExamDetails = collect($headerExamDetails)->unique('id')->values();
                                        }
                                    @endphp
                                    @foreach($headerExamDetails as $detail)
                                        @php
                                            $headerDetailId = is_object($detail) ? $detail->id : (is_array($detail) ? ($detail['id'] ?? null) : null);
                                            $headerSettings = $headerSemesterId && $headerDetailId ? ($examSettingsBySemester[$headerSemesterId][$headerDetailId] ?? null) : null;
                                            
                                            // Handle examName relationship safely
                                            $headerDetailName = 'N/A';
                                            if (is_object($detail)) {
                                                $examName = $detail->examName ?? null;
                                                if (is_object($examName)) {
                                                    $headerDetailName = $examName->name ?? 'N/A';
                                                }
                                            } elseif (is_array($detail) && isset($detail['exam_name'])) {
                                                $headerDetailName = is_string($detail['exam_name']) ? $detail['exam_name'] : 'N/A';
                                            }
                                            
                                            // Handle examType relationship safely
                                            $headerDetailType = '';
                                            if (is_object($detail)) {
                                                $examType = $detail->examType ?? null;
                                                if (is_object($examType)) {
                                                    $headerDetailType = $examType->name ?? '';
                                                }
                                            } elseif (is_array($detail) && isset($detail['exam_type'])) {
                                                $headerDetailType = is_string($detail['exam_type']) ? $detail['exam_type'] : '';
                                            }
                                            
                                            $headerFullMark = is_object($headerSettings) ? ($headerSettings->full_mark ?? null) : (is_array($headerSettings) ? ($headerSettings['full_mark'] ?? null) : null);
                                        @endphp
                                        <th class="px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase tracking-wider border-l border-gray-200">
                                            <div class="text-xs">{{ $headerDetailName }}</div>
                                            <div class="text-xs text-gray-500">{{ $headerDetailType }}</div>
                                            @if($headerSettings && $headerFullMark)
                                                <div class="text-xs text-blue-600 font-semibold">Max: {{ $headerFullMark }}</div>
                                            @endif
                                        </th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                // Ensure students are unique by ID
                                $uniqueStudents = $students->unique('id')->values();
                            @endphp
                            @foreach($uniqueStudents as $studentKey => $student)
                                @php
                                    $studentId = is_object($student) ? $student->id : (is_array($student) ? ($student['id'] ?? null) : null);
                                @endphp
                                @if($studentId)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 sticky left-0 bg-white">
                                        {{ is_object($student) ? ($student->roll_no ?? 'N/A') : ($student['roll_no'] ?? 'N/A') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 sticky left-16 bg-white">
                                        @php
                                            $studentName = 'N/A';
                                            $boardRegNo = is_object($student) ? ($student->board_reg_no ?? 'N/A') : 'N/A';
                                            if (is_object($student) && $student->studentdb) {
                                                $studentdb = $student->studentdb;
                                                $studentName = is_object($studentdb) ? ($studentdb->student_name ?? 'N/A') : 'N/A';
                                            }
                                        @endphp
                                        {{ $studentName }}<br>
                                        {{ $boardRegNo }}
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap border-l border-gray-200">
                                        <button 
                                            wire:click="saveStudentMarks({{ $studentId }})"
                                            class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        >
                                            Save
                                        </button>
                                    </td>
                                    
                                    @foreach($uniqueSemesters as $semesterKey => $semester)
                                        @php
                                            $semesterId = is_object($semester) ? $semester->id : (is_array($semester) ? ($semester['id'] ?? null) : null);
                                        @endphp
                                        @if($semesterId)
                                            @php
                                                $semesterExamDetails = $examDetailsBySemester[$semesterId] ?? [];
                                                // Ensure exam details are unique by ID
                                                if (is_object($semesterExamDetails) || is_array($semesterExamDetails)) {
                                                    $semesterExamDetails = collect($semesterExamDetails)->unique('id')->values();
                                                }
                                            @endphp
                                            @foreach($semesterExamDetails as $detailKey => $detail)
                                                @php
                                                    $detailId = is_object($detail) ? $detail->id : (is_array($detail) ? ($detail['id'] ?? null) : null);
                                                    $setting = ($examSettingsBySemester[$semesterId] ?? [])[$detailId] ?? null;
                                                    $studentMarks = $marksData[$studentId] ?? [];
                                                    $semesterMarks = $studentMarks[$semesterId] ?? [];
                                                    $markEntry = $semesterMarks[$detailId] ?? null;
                                                    $isSaved = $markEntry && is_array($markEntry) && ($markEntry['marks_obtained'] ?? null) !== null;
                                                    $fullMark = null;
                                                    if ($setting) {
                                                        $fullMark = is_object($setting) ? ($setting->full_mark ?? null) : (is_array($setting) ? ($setting['full_mark'] ?? null) : null);
                                                    }
                                                @endphp
                                                <td class="px-2 py-3 whitespace-nowrap border-l border-gray-200">
                                                    @if($setting && $fullMark !== null)
                                                        <input 
                                                            type="number" 
                                                            min="0" 
                                                            max="{{ $fullMark }}"
                                                            placeholder="0-{{ $fullMark }}"
                                                            wire:model.defer="marksData.{{ $studentId }}.{{ $semesterId }}.{{ $detailId }}.marks_obtained"
                                                            class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring focus:ring-blue-200"
                                                        />
                                                        @if($isSaved && $markEntry)
                                                            <div class="text-xs text-green-600 mt-1">
                                                                Saved: {{ $markEntry['marks_obtained'] }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="text-xs text-gray-400">No setting</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <button 
                        wire:click="saveMarks"
                        class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold"
                    >
                        Save All Marks
                    </button>
                </div>
            </div>
        @elseif($selectedClassId && $selectedSubjectId)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-800">
                    @if(count($students) === 0)
                        No students found for this class.
                    @elseif(count($semesters) === 0)
                        No semesters or exam details configured for this class and subject combination.
                    @endif
                </p>
            </div>
        @endif

        {{-- Instructions --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h4 class="font-semibold text-blue-900 mb-2">Instructions:</h4>
            <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800">
                <li>Session and School are automatically selected from your authenticated user account</li>
                <li>Select Class and Subject from the dropdown menus</li>
                <li>Students will be displayed ordered by their Board Registration Number</li>
                <li>For each semester, enter marks in the textboxes provided for each exam detail</li>
                <li>The maximum marks for each exam are displayed in the header</li>
                <li>Click "Save All Marks" to save the entered marks</li>
                <li>Existing saved marks will be updated, new marks will be created</li>
            </ol>
        </div>
    </div>
</div>
