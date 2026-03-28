<div>
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Exam for Marks Entry</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Session --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Session *</label>
                <select wire:model="selected_session_id"
                    class="w-full rounded border-gray-300 @error('selected_session_id') border-red-500 @enderror">
                    <option value="">-- Select Session --</option>
                    @foreach($sessionOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selected_session_id') <span class="text-xs text-red-500">{{ $message }}</span>@enderror
            </div>

            {{-- School --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">School *</label>
                <select wire:model="selected_school_id"
                    class="w-full rounded border-gray-300 @error('selected_school_id') border-red-500 @enderror">
                    <option value="">-- Select School --</option>
                    @foreach($schoolOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selected_school_id') <span class="text-xs text-red-500">{{ $message }}</span>@enderror
            </div>

            {{-- Class --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                <select wire:model="selected_myclass_id"
                    class="w-full rounded border-gray-300 @error('selected_myclass_id') border-red-500 @enderror">
                    <option value="">-- Select Class --</option>
                    @foreach($myclassOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selected_myclass_id') <span class="text-xs text-red-500">{{ $message }}</span>@enderror
            </div>

            {{-- Semester --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                <select wire:model="selected_semester_id"
                    class="w-full rounded border-gray-300 @error('selected_semester_id') border-red-500 @enderror">
                    <option value="">-- Select Semester --</option>
                    @foreach($semesterOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selected_semester_id') <span class="text-xs text-red-500">{{ $message }}</span>@enderror
            </div>

            {{-- Exam Detail --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exam Detail *</label>
                <select wire:model="selected_exam_detail_id"
                    class="w-full rounded border-gray-300 @error('selected_exam_detail_id') border-red-500 @enderror">
                    <option value="">-- Select Exam --</option>
                    @foreach($examDetailOptions as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('selected_exam_detail_id') <span class="text-xs text-red-500">{{ $message }}</span>@enderror
            </div>

            {{-- Subject --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                <select wire:model="selected_subject_id"
                    class="w-full rounded border-gray-300 @error('selected_subject_id') border-red-500 @enderror">
                    <option value="">-- Select Subject --</option>
                    @foreach($subjectOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selected_subject_id') <span class="text-xs text-red-500">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="mt-4 text-right">
            <button wire:click="$refresh" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Refresh
            </button>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class / Semester</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Details</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subjects</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $rowIndex = 0;
                @endphp
                @forelse($groupedSettings as $key => $group)
                    @php
                        $examDetail = $group['exam_detail'];
                        $settingsCount = count($group['settings']);
                    @endphp
                    <tr class="{{ $rowIndex % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 align-top">
                            <div class="font-medium text-gray-900">{{ $group['myclass']->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $group['semester']->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $examDetail->examName->name ?? 'N/A' }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $examDetail->examType->name ?? 'N/A' }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $examDetail->examPart->name ?? 'N/A' }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ({{ $examDetail->examMode->name ?? 'N/A' }})
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach($group['settings'] as $setting)
                                    <button wire:click="openMarksEntry({{ $setting->id }})" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                                                                            bg-indigo-100 text-indigo-800 border border-indigo-200 
                                                                            hover:bg-indigo-200 hover:border-indigo-300 transition
                                                                            shadow-sm" title="Click to enter marks">
                                        <span class="font-bold">{{ $setting->subject->subject_code ?? '' }}</span>
                                        <span class="truncate max-w-[120px]">{{ $setting->subject->name ?? 'N/A' }}</span>
                                        <span
                                            class="text-xs bg-indigo-200 px-1.5 py-0.5 rounded">FM:{{ $setting->full_mark }}</span>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @php
                        $rowIndex++;
                    @endphp
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            No exam settings configured. Please configure exams first.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-4 py-3 bg-gray-50">
            {{ $settings->links() }}
        </div>
    </div>

    {{-- Marks Entry Modal --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('isOpen') }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                {{-- Modal Panel --}}
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">

                    {{-- Modal Header --}}
                    <div
                        class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <div class="text-white">
                            <h3 class="text-lg font-bold">
                                {{ $isViewMode ? 'View Student Marks' : 'Enter Marks' }}
                            </h3>
                            <p class="text-sm text-indigo-100">
                                {{ $currentSubjectName }} | Full Mark: {{ $currentFullMark }} | Pass Mark:
                                {{ $currentPassMark }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$isViewMode && count($studentList) > 0)
                                <button wire:click="deleteAllMarks"
                                    onclick="return confirm('Delete all marks for this subject?')"
                                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-md transition">
                                    Clear All
                                </button>
                            @endif
                            <button wire:click="closeModal" class="text-white hover:text-indigo-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto">
                        @if($isViewMode && $viewingStudentId)
                            {{-- View Mode --}}
                            <div class="mb-4 flex justify-end">
                                <button wire:click="backToEntryMode"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                    Back to List
                                </button>
                            </div>
                            @if(count($viewingMarks) > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Full Mark
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Obtained
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Percentage
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Grade</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($viewingMarks as $mark)
                                                    <tr>
                                                        <td class="px-3 py-2 font-medium">{{ $mark->subject->name ?? 'N/A' }}</td>
                                                        <td class="px-3 py-2 text-center">{{ $mark->examSetting->full_mark ?? 'N/A' }}</td>
                                                        <td class="px-3 py-2 text-center font-bold text-indigo-600">{{ $mark->marks_obtained }}
                                                        </td>
                                                        <td class="px-3 py-2 text-center">{{ $mark->marks_percentage }}%</td>
                                                        <td class="px-3 py-2 text-center">
                                                            <span
                                                                class="px-2 py-1 rounded-full text-xs font-bold
                                                                                                                                                        {{ $mark->marks_grade == 'A+' || $mark->marks_grade == 'A' ? 'bg-green-100 text-green-800' :
                                            ($mark->marks_grade == 'F' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                                {{ $mark->marks_grade }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 text-center">
                                                            <button wire:click="deleteMarksEntry({{ $mark->id }})"
                                                                class="text-red-500 hover:text-red-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <p>No marks found for this student.</p>
                                </div>
                            @endif
                        @else
                            {{-- Entry Mode --}}
                            @if(count($studentList) > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16">Roll No
                                            </th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student Name
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Class
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">
                                                Section</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">
                                                Semester</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-32">Marks
                                                Obtained</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">%
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Grade
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($studentList as $index => $student)
                                            @php
                                                $marks = $marksData[$student['id']] ?? ['marks_obtained' => null, 'percentage' => null, 'grade' => null, 'marks_entry_id' => null];
                                            @endphp
                                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                                <td class="px-3 py-2 font-medium text-gray-900">{{ $student['roll_no'] }}</td>
                                                <td class="px-3 py-2 font-medium text-gray-900">{{ $student['name'] }}</td>
                                                <td class="px-3 py-2 text-center text-sm text-gray-600">Class
                                                    #{{ $student['myclass_id'] }}</td>
                                                <td class="px-3 py-2 text-center text-sm text-gray-600">Section
                                                    #{{ $student['section_id'] }}</td>
                                                <td class="px-3 py-2 text-center text-sm text-gray-600">Sem
                                                    #{{ $student['semester_id'] }}</td>
                                                <td class="px-3 py-2">
                                                    <input type="number" wire:model="marksData.{{ $student['id'] }}.marks_obtained"
                                                        min="0" max="{{ $currentFullMark }}" placeholder="0"
                                                        class="w-full rounded border-gray-300 text-center focus:ring-indigo-500 focus:border-indigo-500">
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    @if($marks['percentage'] !== null)
                                                        <span class="font-bold text-indigo-600">{{ $marks['percentage'] }}%</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    @if($marks['grade'])
                                                                    <span
                                                                        class="px-2 py-1 rounded-full text-xs font-bold
                                                                                                                                                                                            {{ $marks['grade'] == 'A+' || $marks['grade'] == 'A' ? 'bg-green-100 text-green-800' :
                                                        ($marks['grade'] == 'F' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                                        {{ $marks['grade'] }}
                                                                    </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    @if($marks['marks_entry_id'])
                                                        <button wire:click="viewStudentMarks({{ $student['id'] }})"
                                                            class="text-indigo-600 hover:text-indigo-800" title="View All Marks">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <p>No students found for this class/semester.</p>
                                    <p class="text-sm">Please add students first.</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    @if(!$isViewMode)
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-3">
                            <button wire:click="closeModal"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button wire:click="saveMarks" @if(count($studentList) == 0) disabled @endif
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Marks
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush