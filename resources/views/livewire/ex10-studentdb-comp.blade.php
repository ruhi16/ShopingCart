<div class="p-6 bg-white rounded-lg shadow" wire:key="ex10-studentdb-container">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Student Database Management</h2>
        <button wire:click="create()"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
            + Add Student
        </button>
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
            <input type="text" wire:model.debounce.300ms="search" placeholder="Name, Code, Aadhaar..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
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
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Father</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subjects</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">
                            {{ $student->student_name ?? 'N/A' }}
                            <span class="text-gray-400 text-xs block">{{ $student->student_code ?? '-' }}</span>
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $student->father_name ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">
                            {{ $student->contact_number1 ?? '-' }}
                            @if($student->contact_number2)
                                <span class="text-gray-400 text-xs block">{{ $student->contact_number2 }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-900">
                            {{ $student->admissionMyclass->name ?? '-' }}
                            <span class="text-gray-400 text-xs block">
                                {{ $student->admissionSemester->name ?? '' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-sm">
                            @if($student->studentSubjects && $student->studentSubjects->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($student->studentSubjects as $studentSubject)
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                            {{ $studentSubject->subject->name ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">No subjects</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($student->is_active)
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                            @else
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <button wire:click="edit({{ $student->id }})"
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-2">Edit</button>
                            <button wire:click="delete({{ $student->id }})"
                                wire:confirm="Are you sure you want to delete this student?"
                                class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $students->links() }}
    </div>

    {{-- Modal for Create/Edit --}}
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
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $student_id ? 'Edit Student' : 'Add New Student' }}
                            </h3>
                        </div>

                        {{-- Form --}}
                        <form wire:submit.prevent="store()">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- Basic Info --}}
                                <div class="md:col-span-3">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 border-b pb-1">Basic Information
                                    </h4>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Student Name *</label>
                                    <input type="text" wire:model="student_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('student_name') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Student Code</label>
                                    <input type="text" wire:model="student_code"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
                                    <select wire:model="gender"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select --</option>
                                        @foreach($genderOptions as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
                                    <input type="date" wire:model="date_of_birth"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Father's Name</label>
                                    <input type="text" wire:model="father_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Mother's Name</label>
                                    <input type="text" wire:model="mother_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- Contact Info --}}
                                <div class="md:col-span-3">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 border-b pb-1 mt-4">Contact
                                        Information</h4>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Contact 1</label>
                                    <input type="text" wire:model="contact_number1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Contact 2</label>
                                    <input type="text" wire:model="contact_number2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Village</label>
                                    <input type="text" wire:model="village"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Post Office</label>
                                    <input type="text" wire:model="post_office"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Police Station</label>
                                    <input type="text" wire:model="police_station"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">District</label>
                                    <input type="text" wire:model="district"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Pin Code</label>
                                    <input type="text" wire:model="pin_code"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nationality</label>
                                    <input type="text" wire:model="nationality"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Caste</label>
                                    <input type="text" wire:model="caste"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Religion</label>
                                    <input type="text" wire:model="religion"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- Admission Info --}}
                                <div class="md:col-span-3">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 border-b pb-1 mt-4">Admission
                                        Details</h4>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Admission No</label>
                                    <input type="text" wire:model="admission_number"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Admission Date</label>
                                    <input type="date" wire:model="admission_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Class</label>
                                    <select wire:model="admission_myclass_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Class --</option>
                                        @foreach($myclassOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                                    <select wire:model="admission_semester_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Semester --</option>
                                        @foreach($semesterOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Section</label>
                                    <select wire:model="admission_section_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Section --</option>
                                        @foreach($sectionOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Board Reg No</label>
                                    <input type="text" wire:model="board_reg_no"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Board Roll No</label>
                                    <input type="text" wire:model="board_roll_no"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- Subjects Selection --}}
                                <div class="md:col-span-3">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 border-b pb-1 mt-4">Subjects *</h4>
                                    <p class="text-xs text-gray-500 mb-2">Select subjects for this student:</p>

                                    @if(count($available_subjects) > 0)
                                        <div
                                            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3 bg-gray-50">
                                            @foreach($available_subjects as $subject)
                                                <label
                                                    class="flex items-center space-x-2 cursor-pointer hover:bg-white p-1 rounded">
                                                    <input type="checkbox" wire:model="selected_subjects" value="{{ $subject->id }}"
                                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                    <span class="text-sm text-gray-700">{{ $subject->name }}</span>
                                                    @if($subject->subject_code)
                                                        <span class="text-xs text-gray-400">({{ $subject->subject_code }})</span>
                                                    @endif
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-orange-500 italic">No subjects available for the selected session
                                            and school.</p>
                                    @endif
                                </div>

                                {{-- Document Info --}}
                                <div class="md:col-span-3">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 border-b pb-1 mt-4">Documents & ID
                                    </h4>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Aadhaar Number</label>
                                    <input type="text" wire:model="aadhaar_number"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Birth Certificate No</label>
                                    <input type="text" wire:model="birth_certificate_number"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- Bank Info --}}
                                <div class="md:col-span-3">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 border-b pb-1 mt-4">Bank Details
                                    </h4>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Account Number</label>
                                    <input type="text" wire:model="bank_account_number"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Account Type</label>
                                    <select wire:model="bank_account_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select --</option>
                                        @foreach($bankAccountTypeOptions as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name</label>
                                    <input type="text" wire:model="bank_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                                    <input type="text" wire:model="bank_branch"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">IFSC Code</label>
                                    <input type="text" wire:model="bank_ifsc_code"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- Status --}}
                                <div class="md:col-span-3">
                                    <div class="flex items-center mt-4">
                                        <input type="checkbox" wire:model="is_active" id="is_active"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                                    </div>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                                    <textarea wire:model="remarks" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" wire:click="closeModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    {{ $student_id ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>