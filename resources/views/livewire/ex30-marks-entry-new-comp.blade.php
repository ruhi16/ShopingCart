<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Marks Entry</h2>
            <p class="text-sm text-gray-600 mt-1">Enter marks for students by subject and exam</p>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
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
            
            @if($selectedClassId && $selectedSubjectId)
                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                    <p>
                        <strong>ℹ️ Selected:</strong> 
                        Class ID: {{ $selectedClassId }} | 
                        Subject ID: {{ $selectedSubjectId }} |
                        Students Found: {{ count($students) }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Marks Entry Table --}}
        @if(count($students) > 0 && count($examDetails) > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Marks Entry Form - {{ $students->count() }} Students
                    </h3>
                    <div class="space-x-2">
                        <button 
                            wire:click="saveAllMarks"
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                            Save All Marks
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-blue-50 border-b-2 border-blue-200">
                                <th class="px-3 py-3 text-left font-semibold text-blue-700 uppercase tracking-wide border-r border-blue-200 w-16">
                                    Roll
                                </th>
                                <th class="px-3 py-3 text-left font-semibold text-blue-700 uppercase tracking-wide border-r border-blue-200 min-w-[200px]">
                                    Student Name
                                </th>
                                
                                @foreach($examDetails as $examDetail)
                                    @php
                                        $setting = $examSettings[$examDetail->id] ?? null;
                                    @endphp
                                    <th class="px-2 py-3 text-center font-semibold text-blue-700 uppercase tracking-wide border-r border-blue-200 min-w-[120px]">
                                        <div class="font-semibold text-blue-800 text-xs">
                                            {{ $examDetail->examName->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-blue-600 font-normal text-xs">
                                            {{ $examDetail->semester->name ?? '' }}
                                            ·
                                            {{ $examDetail->examType->name ?? '' }}
                                            ·
                                            {{ $examDetail->examMode->name ?? '' }}
                                        </div>
                                        @if($setting)
                                            <div class="text-xs text-blue-700 font-semibold mt-1">
                                                Max: {{ $setting->full_mark }}
                                            </div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        
                        <tbody class="divide-y divide-gray-100">
                            @foreach($students as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-700 font-medium border-r border-gray-200">
                                        {{ $student->roll_no }}
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200">
                                        <div class="font-medium text-gray-800">
                                            {{ $student->studentdb->student_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $student->currentMyclass->name ?? '' }}
                                            {{ $student->currentSection->name ?? '' }}
                                        </div>
                                    </td>
                                    
                                    @foreach($examDetails as $examDetail)
                                        @php
                                            $setting = $examSettings[$examDetail->id] ?? null;
                                            $existingMark = Ex30MarksEntry::where('studentcr_id', $student->id)
                                                ->where('subject_id', $this->selectedSubjectId)
                                                ->where('exam_detail_id', $examDetail->id)
                                                ->first();
                                        @endphp
                                        <td class="px-2 py-2 text-center border-r border-gray-200">
                                            @if(!$setting)
                                                <span class="text-gray-400 text-xs">No setting</span>
                                            @else
                                                @if($existingMark)
                                                    <div class="mb-1">
                                                        @if($existingMark->is_absent)
                                                            <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">ABSENT</span>
                                                        @else
                                                            <span class="text-sm font-bold text-blue-800">
                                                                {{ $existingMark->marks_obtained }}/{{ $setting->full_mark }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <div class="flex flex-col items-center gap-1">
                                                    <div class="flex items-center gap-1">
                                                        <input 
                                                            type="number" 
                                                            min="0" 
                                                            max="{{ $setting->full_mark }}"
                                                            wire:model.defer="marksData.{{ $student->id }}.{{ $examDetail->id }}.marks_obtained"
                                                            placeholder="Marks"
                                                            class="w-16 px-1 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring focus:ring-blue-200"
                                                            @if($existingMark && $existingMark->is_absent) disabled @endif
                                                        />
                                                        
                                                        <label class="flex items-center text-xs whitespace-nowrap">
                                                            <input 
                                                                type="checkbox"
                                                                wire:model.defer="marksData.{{ $student->id }}.{{ $examDetail->id }}.is_absent"
                                                                class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                                                            />
                                                            <span class="ml-1 text-gray-600">ABS</span>
                                                        </label>
                                                    </div>
                                                    
                                                    <button 
                                                        wire:click="saveSingleMark({{ $student->id }}, {{ $examDetail->id }})"
                                                        class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 w-full"
                                                    >
                                                        Save
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($selectedClassId && $selectedSubjectId)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-800">
                    @if(count($students) === 0)
                        No students found with the selected subject. Make sure students have enrolled in this subject.
                    @elseif(count($examDetails) === 0)
                        No exam details configured for this class. Please set up exam details first.
                    @endif
                </p>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <p class="text-blue-800">
                    Select a class and subject to begin entering marks.
                </p>
            </div>
        @endif

        {{-- Instructions --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h4 class="font-semibold text-blue-900 mb-2">Instructions:</h4>
            <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800">
                <li>Session and School are automatically selected from your authenticated user account</li>
                <li>Select Class and Subject from the dropdown menus</li>
                <li>Only students enrolled in the selected subject will be displayed</li>
                <li>For each exam, enter obtained marks or mark as absent</li>
                <li>The maximum marks for each exam are displayed in the header</li>
                <li>Click "Save All Marks" to save the entered marks</li>
                <li>Existing saved marks will be updated, new marks will be created</li>
            </ol>
        </div>
    </div>
</div>
