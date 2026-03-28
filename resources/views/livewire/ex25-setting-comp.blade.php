<div>
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Session</label>
                <select wire:model="selected_session_id" class="w-full rounded border-gray-300">
                    <option value="">-- Select Session --</option>
                    @foreach($sessionOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                <select wire:model="selected_school_id" class="w-full rounded border-gray-300">
                    <option value="">-- Select School --</option>
                    @foreach($schoolOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Class</label>
                <input type="text" wire:model="search" placeholder="Search by class name..."
                    class="w-full rounded border-gray-300">
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class / Semester</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Details</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($myclassSemesters as $item)
                    @if(count($item->examDetailsWithCount) > 0)
                        @foreach($item->examDetailsWithCount as $index => $data)
                            @php
                                $examDetail = $data['detail'];
                                $settingsCount = $data['settings_count'];
                            @endphp
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                @if($index == 0)
                                    <td rowspan="{{ count($item->examDetailsWithCount) }}" class="px-4 py-3 align-top">
                                        <div class="font-medium text-gray-900">{{ $item->myclass->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->semester->name ?? 'N/A' }}</div>
                                    </td>
                                @endif
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 flex-wrap mb-2">
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

                                    {{-- Configured Subjects as Chips --}}
                                    @if($data['settings']->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($data['settings'] as $setting)
                                                <div class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium
                                                                                            {{ $setting->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}"
                                                    title="{{ $setting->subject->name ?? 'N/A' }} - FM: {{ $setting->full_mark }}, PM: {{ $setting->pass_mark }}, Time: {{ $setting->time_in_minutes }} min">
                                                    <span class="font-bold">{{ $setting->subject->subject_code ?? '' }}</span>
                                                    <span class="truncate max-w-[80px]">{{ $setting->subject->name ?? 'N/A' }}</span>
                                                    <span class="text-emerald-600 font-bold">FM:{{ $setting->full_mark }}</span>
                                                    <span class="text-orange-600 font-bold">PM:{{ $setting->pass_mark }}</span>
                                                    <button wire:click="editSetting({{ $setting->id }})"
                                                        class="ml-1 text-blue-500 hover:text-blue-700" title="Edit">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="configureExamSettings({{ $item->id }}, {{ $examDetail->id }})"
                                            class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-md transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Configure
                                        </button>
                                        @if($settingsCount > 0)
                                            <button wire:click="deleteAllSettingsForExamDetail({{ $item->id }}, {{ $examDetail->id }})"
                                                onclick="return confirm('Delete all settings for this exam detail?')"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-md transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Clear ({{ $settingsCount }})
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $item->myclass->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $item->semester->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-sm">No exam details configured</td>
                            <td class="px-4 py-3 text-center text-gray-500 text-sm">-</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            No class/semester combinations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-4 py-3 bg-gray-50">
            {{ $myclassSemesters->links() }}
        </div>
    </div>

    {{-- Settings Modal --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('isOpen') }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                {{-- Modal Panel --}}
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">

                    {{-- Modal Header --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                Configure Exam Settings
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ $currentMyclassName }} - {{ $currentSemesterName }}
                            </p>
                            <p class="text-sm text-indigo-600 font-medium">
                                {{ $currentExamDetailName }}
                            </p>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto">
                        @if(count($subjectSettings) > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Full
                                            Mark</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Pass
                                            Mark</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Time
                                            (Min)</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">
                                            Active</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($subjectSettings as $index => $setting)
                                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                            <td class="px-3 py-2">
                                                <div class="font-medium text-gray-900">{{ $setting['subject_name'] ?? 'N/A' }}</div>
                                                @if($setting['subject_code'])
                                                    <div class="text-xs text-gray-500">{{ $setting['subject_code'] }}</div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" wire:model="subjectSettings.{{ $index }}.full_mark" min="0"
                                                    max="500"
                                                    class="w-full rounded border-gray-300 text-center focus:ring-indigo-500 focus:border-indigo-500">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" wire:model="subjectSettings.{{ $index }}.pass_mark" min="0"
                                                    class="w-full rounded border-gray-300 text-center focus:ring-indigo-500 focus:border-indigo-500">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" wire:model="subjectSettings.{{ $index }}.time_in_minutes"
                                                    min="0"
                                                    class="w-full rounded border-gray-300 text-center focus:ring-indigo-500 focus:border-indigo-500">
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <input type="checkbox" wire:model="subjectSettings.{{ $index }}.is_active"
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No subjects available for this class.</p>
                                <p class="text-sm">Please add subjects first.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-3">
                        <button wire:click="closeModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button wire:click="saveSettings" @if(count($subjectSettings) == 0) disabled @endif
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Save All Settings
                        </button>
                    </div>
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