<div class="p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Exam Details Configuration</h2>
        <div class="flex gap-2">
            <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Detail
            </button>
            <button wire:click="generateCombinations()"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Generate All Combinations
            </button>
            <button wire:click="bulkDelete()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                onclick="return confirm('Are you sure you want to delete all details for this session and school?')">
                Bulk Delete
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div>
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Search Exam Name:</label>
                <input type="text" wire:model.debounce.300ms="search"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Search...">
            </div>
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

    <!-- Modal for Create/Edit -->
    @if($isOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ $detail_id ? 'Update Detail' : 'Create Detail' }}
                                </h3>
                                <div class="mt-2">
                                    <form>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="mb-4">
                                                <label for="exam_name_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Exam Name:</label>
                                                <select wire:model.defer="exam_name_id"
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="">Select Exam Name</option>
                                                    @foreach($names as $name)
                                                        <option value="{{ $name->id }}">{{ $name->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('exam_name_id') <span
                                                class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="mb-4">
                                                <label for="exam_type_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Exam Type:</label>
                                                <select wire:model.defer="exam_type_id"
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="">Select Exam Type</option>
                                                    @foreach($types as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('exam_type_id') <span
                                                class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="mb-4">
                                                <label for="exam_part_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Exam Part:</label>
                                                <select wire:model.defer="exam_part_id"
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="">Select Exam Part</option>
                                                    @foreach($parts as $part)
                                                        <option value="{{ $part->id }}">{{ $part->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('exam_part_id') <span
                                                class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="mb-4">
                                                <label for="exam_mode_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Exam Mode:</label>
                                                <select wire:model.defer="exam_mode_id"
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="">Select Exam Mode</option>
                                                    @foreach($modes as $mode)
                                                        <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('exam_mode_id') <span
                                                class="text-red-500 text-xs italic">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="mb-4">
                                                <label for="session_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Session:</label>
                                                <select wire:model.defer="selected_session_id"
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    @foreach($sessions as $session)
                                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label for="school_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">School:</label>
                                                <select wire:model.defer="selected_school_id"
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    @foreach($schools as $school)
                                                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label for="is_active"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                                                <select wire:model.defer="is_active"
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label for="remarks"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Remarks:</label>
                                                <input type="text" wire:model.defer="remarks"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    id="remarks">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click.prevent="store()" type="button"
                            class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save
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

    <!-- Data Table -->
    <div class="mt-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Type
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Part
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Mode
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($details as $key => $detail)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $key + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->examName->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->examType->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->examPart->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->examMode->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->session->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->school->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $detail->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $detail->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $detail->id }})"
                                class="text-indigo-600 hover:text-indigo-900">Edit</button>
                            <button wire:click="toggleStatus({{ $detail->id }})"
                                class="text-blue-600 hover:text-blue-900 ml-2">
                                {{ $detail->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button wire:click="delete({{ $detail->id }})" class="text-red-600 hover:text-red-900 ml-2"
                                onclick="return confirm('Are you sure you want to delete this detail?')">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>