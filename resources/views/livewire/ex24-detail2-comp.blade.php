<div class="p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Exam Details by Class & Semester</h2>
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="session" class="block text-gray-700 text-sm font-bold mb-2">Session:</label>
                <select wire:model.defer="selected_session_id"
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="school" class="block text-gray-700 text-sm font-bold mb-2">School:</label>
                <select wire:model.defer="selected_school_id"
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4">
            <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Search Class:</label>
            <input type="text" wire:model.debounce.300ms="search"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Search by class name...">
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md my-3"
            role="alert">
            <div class="flex">
                <div>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md my-3" role="alert">
            <div class="flex">
                <div>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
            <div class="flex">
                <div>
                    <p class="text-sm">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Data Table -->
    <div class="mt-6">
        @forelse($myclassSemesters as $key => $myclassSemester)
            <div class="bg-white rounded-lg shadow mb-4 overflow-hidden">
                <!-- Main Row - Class/Semester Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-bold text-gray-700">#{{ $key + 1 }}</span>
                            <span
                                class="text-lg font-semibold text-gray-900">Class: {{ $myclassSemester->myclass->name ?? 'N/A' }}</span>
                            <span class="text-gray-500">-</span>
                            <span
                                class="text-lg font-semibold text-gray-900">{{ $myclassSemester->semester->name ?? 'N/A' }} Semester</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $myclassSemester->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $myclassSemester->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <button wire:click="configureExam({{ $myclassSemester->id }})"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Configure Exam Details
                            </button>
                            <button wire:click="deleteClassSemester({{ $myclassSemester->id }})"
                                class="text-red-600 hover:text-red-900 text-sm font-bold ml-2"
                                onclick="return confirm('Are you sure you want to delete this combination?')">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sub-table - Exam Detail Combinations -->
                <div class="p-6">
                    @php
                        $examDetails = \App\Models\Ex24Detail::with(['examName', 'examType', 'examPart', 'examMode'])
                            ->where('myclass_id', $myclassSemester->myclass_id)
                            ->where('semester_id', $myclassSemester->semester_id)
                            ->where('session_id', $myclassSemester->session_id)
                            ->where('school_id', $myclassSemester->school_id)
                            ->orderBy('exam_name_id')
                            ->orderBy('exam_type_id')
                            ->orderBy('exam_part_id')
                            ->get();
                    @endphp

                    @if($examDetails->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No.</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam Name</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam Type</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam Part</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam Mode</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($examDetails as $detailKey => $detail)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $detailKey + 1 }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            {{ $detail->examName->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            {{ $detail->examType->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            {{ $detail->examPart->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            {{ $detail->examMode->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $detail->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $detail->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="editExamDetail({{ $detail->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                Edit
                                            </button>
                                            <button wire:click="toggleExamDetailStatus({{ $detail->id }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                                {{ $detail->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                            <button wire:click="deleteExamDetail({{ $detail->id }})" 
                                                class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Are you sure you want to delete this exam detail?')">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4 text-gray-500">
                            No exam details configured yet. Click "Configure Exam Details" to add.
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-500">No class-semester combinations found</p>
            </div>
        @endforelse
    </div>

    <!-- Modal for Configuring Exam Details -->
    @if($isOpen)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Configure Exam Details for {{ $selectedMyclassName }} - {{ $selectedSemesterName }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-2">
                                    For each Exam Name, select an Exam Type, then add one or more Exam Parts with their
                                    Modes
                                </p>

                                <div class="mt-4 space-y-4">
                                    @foreach($examConfigurations as $configIndex => $configuration)
                                        <div class="border rounded-lg p-4 bg-gray-50">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-semibold text-gray-700">Exam Configuration
                                                    #{{ $configIndex + 1 }}</h4>
                                                @if(count($examConfigurations) > 1)
                                                    <button wire:click="removeExamNameRow({{ $configIndex }})"
                                                        class="text-red-600 hover:text-red-900 text-sm">
                                                        Remove
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <label class="block text-gray-700 text-sm font-bold mb-2">Exam Name:</label>
                                                    <select
                                                        wire:model.defer="examConfigurations.{{ $configIndex }}.exam_name_id"
                                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                        <option value="">Select Exam Name</option>
                                                        @php
                                                            $examNames = \App\Models\Ex20Name::active()->get();
                                                        @endphp
                                                        @foreach($examNames as $name)
                                                            <option value="{{ $name->id }}">{{ $name->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("examConfigurations.{$configIndex}.exam_name_id")
                                                        <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 text-sm font-bold mb-2">Exam Type:</label>
                                                    <select
                                                        wire:model.defer="examConfigurations.{{ $configIndex }}.exam_type_id"
                                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                        <option value="">Select Exam Type</option>
                                                        @php
                                                            $examTypes = \App\Models\Ex21Type::active()->get();
                                                        @endphp
                                                        @foreach($examTypes as $type)
                                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("examConfigurations.{$configIndex}.exam_type_id")
                                                        <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Exam Parts Section -->
                                            <div class="ml-4">
                                                <div class="flex justify-between items-center mb-2">
                                                    <h5 class="text-sm font-semibold text-gray-600">Exam Parts & Modes:</h5>
                                                    <button wire:click="addPartToExam({{ $configIndex }})"
                                                        class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                        + Add Part
                                                    </button>
                                                </div>

                                                @foreach($configuration['exam_parts'] as $partIndex => $examPart)
                                                    <div class="flex gap-2 items-center mb-2">
                                                        <select
                                                            wire:model.defer="examConfigurations.{{ $configIndex }}.exam_parts.{{ $partIndex }}.part_id"
                                                            class="shadow border rounded flex-1 py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                                                            <option value="">Select Exam Part</option>
                                                            @php
                                                                $examParts = \App\Models\Ex22Part::all();
                                                            @endphp
                                                            @foreach($examParts as $part)
                                                                <option value="{{ $part->id }}">{{ $part->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select
                                                            wire:model.defer="examConfigurations.{{ $configIndex }}.exam_parts.{{ $partIndex }}.mode_id"
                                                            class="shadow border rounded flex-1 py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                                                            <option value="">Select Exam Mode</option>
                                                            @php
                                                                $examModes = \App\Models\Ex23Mode::all();
                                                            @endphp
                                                            @foreach($examModes as $mode)
                                                                <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if(count($configuration['exam_parts']) > 1)
                                                            <button
                                                                wire:click="removePartFromExam({{ $configIndex }}, {{ $partIndex }})"
                                                                class="text-red-600 hover:text-red-900 text-sm font-bold px-2">
                                                                ×
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error("examConfigurations.{$configIndex}.exam_parts.{$partIndex}.part_id")
                                                        <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                                    @enderror
                                                    @error("examConfigurations.{$configIndex}.exam_parts.{$partIndex}.mode_id")
                                                        <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                                    @enderror
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    <button wire:click="addExamNameRow"
                                        class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        + Add Another Exam Name
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click.prevent="saveExamConfigurations()" type="button"
                            class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save All Configurations
                        </button>
                        <button wire:click="closeModal()" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    @if($isEditModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Edit Exam Detail
                            </h3>
                            <div class="mt-4">
                                <form>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="editExamNameId" class="block text-gray-700 text-sm font-bold mb-2">Exam Name:</label>
                                            <select wire:model.defer="editExamNameId" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Select Exam Name</option>
                                                @php
                                                    $examNames = \App\Models\Ex20Name::active()->get();
                                                @endphp
                                                @foreach($examNames as $name)
                                                    <option value="{{ $name->id }}">{{ $name->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editExamNameId') <span class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="editExamTypeId" class="block text-gray-700 text-sm font-bold mb-2">Exam Type:</label>
                                            <select wire:model.defer="editExamTypeId" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Select Exam Type</option>
                                                @php
                                                    $examTypes = \App\Models\Ex21Type::active()->get();
                                                @endphp
                                                @foreach($examTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editExamTypeId') <span class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="editExamPartId" class="block text-gray-700 text-sm font-bold mb-2">Exam Part:</label>
                                            <select wire:model.defer="editExamPartId" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Select Exam Part</option>
                                                @php
                                                    $examParts = \App\Models\Ex22Part::all();
                                                @endphp
                                                @foreach($examParts as $part)
                                                    <option value="{{ $part->id }}">{{ $part->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editExamPartId') <span class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="editExamModeId" class="block text-gray-700 text-sm font-bold mb-2">Exam Mode:</label>
                                            <select wire:model.defer="editExamModeId" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Select Exam Mode</option>
                                                @php
                                                    $examModes = \App\Models\Ex23Mode::all();
                                                @endphp
                                                @foreach($examModes as $mode)
                                                    <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editExamModeId') <span class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="editIsActive" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                                            <select wire:model.defer="editIsActive" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="editRemarks" class="block text-gray-700 text-sm font-bold mb-2">Remarks:</label>
                                            <input type="text" wire:model.defer="editRemarks" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="editRemarks">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click.prevent="updateExamDetail()" type="button" 
                        class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Update
                    </button>
                    <button wire:click="closeEditModal()" type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>