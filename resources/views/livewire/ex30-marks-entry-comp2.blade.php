<div class="p-6 bg-white rounded-lg shadow" wire:key="ex30-marks-entry-comp2">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Marks Entry by Subject</h2>
        
        {{-- Enable/Disable Marks Entry Toggle - Right Corner --}}
        <div class="flex items-center">
            <button 
                wire:click="toggleMarksEntry"
                type="button"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $marks_entry_enabled ? 'bg-green-500' : 'bg-gray-300' }}">
                <span class="sr-only">Enable Marks Entry</span>
                <span class="inline-flex h-4 w-4 transform items-center justify-center rounded-full bg-white transition-transform duration-200 {{ $marks_entry_enabled ? 'translate-x-6' : 'translate-x-1' }}">
                    @if($marks_entry_enabled)
                        <svg class="h-3 w-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="h-3 w-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </span>
            </button>
            <span class="ml-3 text-sm font-medium text-gray-700">
                {{ $marks_entry_enabled ? 'Marks Entry: ON' : 'Marks Entry: OFF' }}
            </span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- MyClass (depends on session - auto-selected) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                <select wire:model="selected_myclass_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Class --</option>
                    @foreach($myclassOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Semester (required) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                <select wire:model="selected_semester_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Semester --</option>
                    @foreach($semesterOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Section (optional filter) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Section (Filter)</label>
                <select wire:model="selected_section_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- All Sections --</option>
                    @foreach($sectionOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Exam Detail (required) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exam Detail *</label>
                <select wire:model="selected_exam_detail_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Exam Detail --</option>
                    @foreach($examDetailOptions as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Subject (depends on session and myclass) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                <select wire:model="selected_subject_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Subject --</option>
                    @foreach($subjectOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        {{-- Second Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            {{-- Full Mark --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Full Mark
                    @if($selectedExamSetting && $selectedExamSetting->full_mark)
                        <span class="text-xs text-green-600">(From Setting: {{ $selectedExamSetting->full_mark }})</span>
                    @endif
                </label>
                <input type="number" wire:model="full_mark" min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Exam Detail Info --}}
    @if($selectedExamDetail)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Selected Exam Details:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div>
                    <span class="font-medium text-blue-800">Exam Name:</span>
                    <span class="text-blue-700">{{ $selectedExamDetail->examName->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Exam Type:</span>
                    <span class="text-blue-700">{{ $selectedExamDetail->examType->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Exam Part:</span>
                    <span class="text-blue-700">{{ $selectedExamDetail->examPart->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Exam Mode:</span>
                    <span class="text-blue-700">{{ $selectedExamDetail->examMode->name ?? 'N/A' }}</span>
                </div>
            </div>
            
            {{-- Exam Setting Info --}}
            @if($selectedExamSetting)
                <div class="mt-3 pt-3 border-t border-blue-200">
                    <h4 class="text-xs font-semibold text-blue-900 mb-2">Exam Setting Details:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                        <div>
                            <span class="font-medium text-blue-800">Full Mark:</span>
                            <span class="text-blue-700">{{ $selectedExamSetting->full_mark ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Pass Mark:</span>
                            <span class="text-blue-700">{{ $selectedExamSetting->pass_mark ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Time (min):</span>
                            <span class="text-blue-700">{{ $selectedExamSetting->time_in_minutes ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Setting Name:</span>
                            <span class="text-blue-700">{{ $selectedExamSetting->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Setting ID:</span>
                            <span class="text-blue-700">{{ $exam_setting_id ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-3 pt-3 border-t border-blue-200">
                    <p class="text-xs text-orange-600">
                        <strong>Note:</strong> No exam setting found for this combination. Please configure exam settings in Exam Settings module.
                    </p>
                </div>
            @endif
        </div>
    @endif

    {{-- Students Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if(count($studentList) > 0)
            {{-- Show students list (with or without marks entry based on subject selection and toggle) --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">Roll</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Section</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">Absent</th>
                            @if($selected_subject_id && $selectedExamSetting)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Marks (Max: {{ $full_mark }})</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($studentList as $index => $student)
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $student['roll_no'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $student['name'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if(isset($myclassOptions[$student['myclass_id']]))
                                        {{ $myclassOptions[$student['myclass_id']] }}
                                    @else
                                        {{ $student['myclass_id'] ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if(isset($sectionOptions[$student['section_id']]))
                                        {{ $sectionOptions[$student['section_id']] }}
                                    @else
                                        {{ $student['section_id'] ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($selected_subject_id && $selectedExamSetting && $marks_entry_enabled)
                                        <input type="checkbox" 
                                            wire:model="marksData.{{ $student['id'] }}.is_absent"
                                            class="h-5 w-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    @else
                                        @if(isset($marksData[$student['id']]) && !empty($marksData[$student['id']]['is_absent']))
                                            <span class="text-red-600 font-bold">AB</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @endif
                                </td>
                                @if($selected_subject_id && $selectedExamSetting)
                                    <td class="px-4 py-3 text-center">
                                        @php 
                                            $studentMarksData = $marksData[$student['id']] ?? [];
                                            $isAbsent = !empty($studentMarksData['is_absent']);
                                            $currentMarks = $studentMarksData['marks_obtained'] ?? '';
                                            // Check if marks is -99 (absent)
                                            if ($currentMarks === '-99' || $currentMarks == -99) {
                                                $isAbsent = true;
                                            }
                                            $calculatedGrade = '';
                                            if ($currentMarks !== '' && $currentMarks !== '-99' && !$isAbsent && is_numeric($currentMarks)) {
                                                $marksVal = (float)$currentMarks;
                                                $percentage = $full_mark > 0 ? round(($marksVal / $full_mark) * 100, 2) : 0;
                                                if ($percentage >= 90) $calculatedGrade = 'A+';
                                                elseif ($percentage >= 80) $calculatedGrade = 'A';
                                                elseif ($percentage >= 70) $calculatedGrade = 'B+';
                                                elseif ($percentage >= 60) $calculatedGrade = 'B';
                                                elseif ($percentage >= 50) $calculatedGrade = 'C';
                                                elseif ($percentage >= 40) $calculatedGrade = 'D';
                                                else $calculatedGrade = 'F';
                                            }
                                        @endphp
                                        @if($marks_entry_enabled)
                                            <div class="flex items-center justify-center gap-1">
                                                <input type="number" 
                                                    wire:model="marksData.{{ $student['id'] }}.marks_obtained"
                                                    min="0" max="{{ $full_mark }}" step="0.01"
                                                    @if($isAbsent) disabled @endif
                                                    class="w-16 px-1 py-1 border border-gray-300 rounded-md text-center text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @if($isAbsent) bg-gray-100 text-red-600 font-bold @endif">
                                                @if($isAbsent)
                                                    <span class="text-red-600 font-bold text-sm">AB</span>
                                                @elseif($calculatedGrade)
                                                    <span class="text-green-600 font-bold text-sm">{{ $calculatedGrade }}</span>
                                                @endif
                                            </div>
                                        @else
                                            @if($isAbsent)
                                                <span class="text-red-600 font-bold">AB</span>
                                            @elseif(!empty($currentMarks) && is_numeric($currentMarks))
                                                <span class="text-gray-900">{{ $currentMarks }}</span>
                                                @if($calculatedGrade)
                                                    <span class="text-green-600 font-bold ml-1">{{ $calculatedGrade }}</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($marks_entry_enabled)
                                            <button 
                                                wire:click="saveStudentMarks({{ $student['id'] }})"
                                                class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition">
                                                Save
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Student Count and Status --}}
            <div class="px-4 py-3 bg-gray-50 text-sm text-gray-600 flex justify-between items-center">
                <span>Showing {{ count($studentList) }} students</span>
                @if(!$selected_subject_id)
                    <span class="text-orange-600">Select a subject to enable marks entry</span>
                @elseif(!$selectedExamSetting)
                    <span class="text-red-600">No exam setting found for this combination</span>
                @elseif(!$marks_entry_enabled)
                    <span class="text-blue-600">Enable marks entry toggle to start entering marks</span>
                @endif
            </div>
            
            {{-- Save All Button --}}
            @if($selected_subject_id && $selectedExamSetting && $marks_entry_enabled)
                <div class="px-4 py-3 bg-gray-100 border-t border-gray-200 flex justify-end">
                    <button 
                        wire:click="saveMarks"
                        type="button"
                        class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition font-medium">
                        Save All Marks
                    </button>
                </div>
            @endif
        @else
            <div class="px-4 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Students Found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(!$selected_myclass_id)
                        Please select a class to view students.
                    @elseif(!$selected_semester_id)
                        Please select a semester to view students.
                    @elseif(!$selected_exam_detail_id)
                        Please select an exam detail to view students.
                    @else
                        No students found matching the selected criteria.
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- Info Box --}}
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-blue-800">How it works</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Session and School are auto-selected by default</li>
                        <li>Select Class, Semester, and Section</li>
                        <li>Select Exam Detail (shows Exam Name, Type, Part, and Mode)</li>
                        <li>Students list is shown - filtered by selected class/semester/section</li>
                        <li>Select Subject to enable marks entry for students who have that subject</li>
                        <li>Exam Setting is auto-loaded when subject is selected (provides full_mark, pass_mark, etc.)</li>
                        <li>Click the toggle button to ENABLE/DISABLE marks entry mode</li>
                        <li>Check "Absent" checkbox for absent students - shows "AB" in red and disables marks field</li>
                        <li>Each student has an individual "Save" button to save their marks</li>
                        <li>Data saved includes: studentcr_id, studentdb_id, myclass_id, section_id, semester_id,
                            subject_id, exam_detail_id, exam_setting_id, marks_obtained, percentage, grade, is_absent</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>