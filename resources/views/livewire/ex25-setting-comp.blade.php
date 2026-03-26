<div class="p-6 bg-white rounded-lg shadow" wire:key="ex25-setting-container">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Exam Settings Management</h2>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Session</label>
            <select wire:model="selected_session_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Select Session --</option>
                @foreach($sessionOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
            <select wire:model="selected_school_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Select School --</option>
                @foreach($schoolOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search by class name..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Hierarchical Data Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class /
                        Semester / Exam Detail</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                        Full Mark</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                        Pass Mark</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                        Time (min)</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($myclassSemesters as $csItem)
                    @php
                        $totalExamDetails = $csItem->examDetails->count();
                        $totalSubjects = $csItem->subjects->count();
                    @endphp

                    {{-- Class/Semester Header Row --}}
                    <tr class="bg-blue-50 font-semibold">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $csItem->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900" colspan="6">
                            <span class="text-blue-800">{{ $csItem->myclass->name ?? 'N/A' }}</span>
                            <span class="text-gray-500"> / </span>
                            <span class="text-blue-800">{{ $csItem->semester->name ?? 'N/A' }}</span>
                            <span class="ml-2 text-xs text-gray-500">({{ $totalExamDetails }} exam details,
                                {{ $totalSubjects }} subjects)</span>
                        </td>
                    </tr>

                    @if($totalExamDetails == 0)
                        {{-- No exam details message --}}
                        <tr>
                            <td class="px-4 py-2"></td>
                            <td colspan="6" class="px-4 py-2 text-sm text-orange-500 italic">
                                No exam details configured for this class/semester. Please configure exam details first.
                            </td>
                        </tr>
                    @else
                        @foreach($csItem->examDetails as $examDetail)
                            @php
                                $examDetailRowspan = $totalSubjects > 0 ? $totalSubjects : 1;
                            @endphp

                            {{-- Exam Detail Row --}}
                            <tr class="bg-green-50">
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2 text-sm text-gray-800 font-medium" rowspan="{{ $examDetailRowspan }}">
                                    <span class="text-green-700">
                                        {{ $examDetail->examName->name ?? 'N/A' }}
                                    </span>
                                    <span class="text-gray-400"> - </span>
                                    <span class="text-green-700">
                                        {{ $examDetail->examType->name ?? 'N/A' }}
                                    </span>
                                    <span class="text-gray-400"> - </span>
                                    <span class="text-green-700">
                                        {{ $examDetail->examPart->name ?? 'N/A' }}
                                    </span>
                                    <span class="text-gray-400"> (</span>
                                    <span class="text-green-700">
                                        {{ $examDetail->examMode->name ?? 'N/A' }}
                                    </span>
                                    <span class="text-gray-400">)</span>
                                </td>

                                @if($totalSubjects == 0)
                                    <td colspan="4" class="px-4 py-2 text-sm text-gray-500 italic">No subjects available</td>
                                @else
                                    {{-- First subject row --}}
                                    @php
                                        $firstSubject = $csItem->subjects->first();
                                        $settingsKey = $csItem->id . '_' . $examDetail->id . '_' . $firstSubject->id;
                                        $existingSetting = $csItem->settingsMap->get($settingsKey);
                                        $fullMark = $existingSetting->full_mark ?? 100;
                                        $passMark = $existingSetting->pass_mark ?? 33;
                                        $timeMinutes = $existingSetting->time_in_minutes ?? 60;
                                    @endphp
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $firstSubject->name ?? 'N/A' }}
                                        <span class="text-gray-400 text-xs">({{ $firstSubject->subject_code ?? '-' }})</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" wire:model="formData.{{ $settingsKey }}.full_mark" value="{{ $fullMark }}"
                                            min="0"
                                            class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" wire:model="formData.{{ $settingsKey }}.pass_mark" value="{{ $passMark }}"
                                            min="0"
                                            class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" wire:model="formData.{{ $settingsKey }}.time_in_minutes"
                                            value="{{ $timeMinutes }}" min="0"
                                            class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-2 text-center" rowspan="{{ $examDetailRowspan }}">
                                        <button type="button" wire:click="saveExamSettings({{ $csItem->id }}, {{ $examDetail->id }})"
                                            class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-medium py-1 px-2 rounded">
                                            Save
                                        </button>
                                        <button type="button" wire:click="deleteExamSettings({{ $csItem->id }}, {{ $examDetail->id }})"
                                            wire:confirm="Delete all settings for this exam?"
                                            class="bg-red-500 hover:bg-red-700 text-white text-xs font-medium py-1 px-2 rounded mt-1">
                                            Clear
                                        </button>
                                    </td>
                                @endif
                            </tr>

                            {{-- Remaining subject rows --}}
                            @foreach($csItem->subjects->skip(1) as $subject)
                                @php
                                    $settingsKey = $csItem->id . '_' . $examDetail->id . '_' . $subject->id;
                                    $existingSetting = $csItem->settingsMap->get($settingsKey);
                                    $fullMark = $existingSetting->full_mark ?? 100;
                                    $passMark = $existingSetting->pass_mark ?? 33;
                                    $timeMinutes = $existingSetting->time_in_minutes ?? 60;
                                @endphp
                                <tr>
                                    <td class="px-4 py-2"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $subject->name ?? 'N/A' }}
                                        <span class="text-gray-400 text-xs">({{ $subject->subject_code ?? '-' }})</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" wire:model="formData.{{ $settingsKey }}.full_mark" value="{{ $fullMark }}"
                                            min="0"
                                            class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" wire:model="formData.{{ $settingsKey }}.pass_mark" value="{{ $passMark }}"
                                            min="0"
                                            class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" wire:model="formData.{{ $settingsKey }}.time_in_minutes"
                                            value="{{ $timeMinutes }}" min="0"
                                            class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            No class-semester combinations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $myclassSemesters->links() }}
    </div>
</div>