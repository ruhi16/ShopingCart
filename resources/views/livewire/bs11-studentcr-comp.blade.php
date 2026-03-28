<div class="p-6 bg-white rounded-lg shadow" wire:key="bs11-studentcr-container">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Student Class Representative (CR) Management</h2>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
            <input type="text" wire:model.debounce.300ms="search" placeholder="Name, Code, Father Name..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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

    {{-- SECTION 1: Students Without CR (To be Assigned) --}}
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">
            Students Without Class Representative
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $unassignedStudents->total() }} students)</span>
        </h3>

        {{-- Data Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Father</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                        
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subjects</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($unassignedStudents as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $student->id }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ $student->student_name ?? 'N/A' }}
                                <span class="text-gray-400 text-xs block">{{ $student->student_code ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $student->father_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ $student->admissionMyclass->name ?? '-' }}
                                <span class="text-gray-400 text-xs block">
                                    {{ $student->admissionSemester->name ?? 'xx' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ $student->admissionSection->name ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                @foreach($student->studentSubjects as $sdbSubject)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-sky-100 text-sky-700 border-sky-200">
                                        {{ $sdbSubject->subject->subject_code }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button wire:click="openAssignModal({{ $student->id }})"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm transition">
                                    Assign Roll
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                All students in this session have been assigned as Class Representatives.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $unassignedStudents->links() }}
        </div>
    </div>

    {{-- SECTION 2: Students With CR (Already Assigned) --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">
            Class Representative Records
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $assignedStudents->total() }} records)</span>
        </h3>

        {{-- Data Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Roll No</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assignedStudents as $studentcr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm font-bold text-blue-600">{{ $studentcr->roll_no }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ $studentcr->studentdb->student_name ?? 'N/A' }}
                                <span class="text-gray-400 text-xs block">
                                    {{ $studentcr->studentdb->student_code ?? '-' }}
                                </span>
                            </td>                            
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ $studentcr->studentdb->admissionMyclass->name ?? '-' }}
                                <span class="text-gray-400 text-xs block">
                                    {{ $studentcr->currentSemester->name ?? '' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                @if(isset($sectionOptions[$studentcr->current_section_id]))
                                    {{ $sectionOptions[$studentcr->current_section_id] }}
                                @else
                                    {{ $studentcr->studentdb->admissionSection->name ?? '-' }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{-- {{ $studentcr->studentdb->studentSubjects }} --}}
                                @foreach($studentcr->studentdb->studentSubjects as $sdbSubject)
                                {{-- @foreach($studentcr as $sdbSubject) --}}
                                    {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-sky-100 text-sky-700 border-sky-200">
                                        {{ $sdbSubject->subject->subject_code }}
                                    </span> --}}
                                    {{-- {{ $sdbSubject->subject_id }} --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-lime-100 text-lime-700 border-lime-200">
                                        {{ $sdbSubject->subject->subject_code }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($studentcr->is_active)
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                                @else
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                <button wire:click="removeFromCr({{ $studentcr->id }})"
                                    wire:confirm="Are you sure you want to remove this student from CR records?"
                                    class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                No Class Representative records found for this session.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $assignedStudents->links() }}
        </div>
    </div>

    {{-- Assign Roll Modal --}}
    @if($isAssignModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show"
            x-on:keydown.escape.window="show = false; @this.closeAssignModal()">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    x-on:click="@this.closeAssignModal()">
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Assign Roll Number
                            </h3>
                        </div>

                        {{-- Form --}}
                        <form wire:submit.prevent="assignRoll()">
                            <div class="space-y-4">
                                {{-- Auto Roll Number Display --}}
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Next Auto Roll Number:</p>
                                            <p class="text-2xl font-bold text-blue-600">{{ $nextAutoRoll }}</p>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <p>Based on current</p>
                                            <p>session records</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Custom Roll Option --}}
                                <div class="border border-gray-200 rounded-md p-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="use_custom_roll"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Use Custom Roll Number</span>
                                    </label>

                                    @if($use_custom_roll)
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Enter Custom Roll
                                                Number</label>
                                            <input type="number" wire:model="custom_roll_no" placeholder="Enter roll number"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <p class="text-xs text-gray-500 mt-1">Note: If the roll number already exists, you
                                                will see an error message.</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Section Selection --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                                    <select wire:model="selected_section_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Section --</option>
                                        @foreach($sectionOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Default section is pre-selected from student
                                        admission record.</p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" wire:click="closeAssignModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                    Assign Roll
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>