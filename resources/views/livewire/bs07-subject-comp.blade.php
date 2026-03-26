<div class="p-6 bg-white rounded-lg shadow" wire:key="bs07-subject-container">
    {{-- Header with Create Button --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Subjects Management</h2>
        <button wire:click="create()"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
            + Create Subject
        </button>
    </div>

    {{-- Search Bar --}}
    <div class="mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search by name or code..."
            class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    {{-- Success Message --}}
    @if(session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Data Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subjects->sortBy('id') as $subject)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $subject->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $subject->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $subject->subject_code ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $subject->subject_type ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $subject->session_id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $subject->school_id }}</td>
                        <td class="px-4 py-3">
                            @if($subject->is_active)
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                            @else
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $subject->remarks ?? '-' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button wire:click="edit({{ $subject->id }})"
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-2">Edit</button>
                            <button wire:click="delete({{ $subject->id }})"
                                wire:confirm="Are you sure you want to delete this subject?"
                                class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500">No subjects found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $subjects->links() }}
    </div>

    {{-- Modal for Create/Edit - Using Livewire's isOpen property directly --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show"
            x-on:keydown.escape.window="show = false; @this.closeModal()">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-on:click="@this.closeModal()">
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                {{ $subject_id ? 'Edit Subject' : 'Create Subject' }}
                            </h3>
                        </div>

                        {{-- Form --}}
                        <form wire:submit.prevent="store()">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                    <input type="text" wire:model="name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Code</label>
                                    <input type="text" wire:model="subject_code"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Type *</label>
                                    <select wire:model="subject_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Type --</option>
                                        <option value="Lab">Lab</option>
                                        <option value="Non-Lab">Non-Lab</option>
                                    </select>
                                    @error('subject_type') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Category</label>
                                    <select wire:model="category_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="0">-- Select Category --</option>
                                        @foreach($categoryOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Session *</label>
                                    <select wire:model="session_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Session --</option>
                                        @foreach($sessionOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('session_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">School *</label>
                                    <select wire:model="school_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select School --</option>
                                        @foreach($schoolOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('school_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex items-center">
                                    <label class="flex items-center mt-6">
                                        <input type="checkbox" wire:model="is_active"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Active</span>
                                    </label>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea wire:model="description" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                    <input type="text" wire:model="remarks"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" wire:click="closeModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>