<div class="p-6">
    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Marks Entry (v3)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Class --}}
            <div>
                <label for="myclass_id" class="block text-sm font-medium text-gray-700 mb-1">Select Class</label>
                <select wire:model="myclass_id" id="myclass_id" 
                    class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">-- Choose Class --</option>
                    @foreach($myclasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Semester --}}
            <div>
                <label for="semester_id" class="block text-sm font-medium text-gray-700 mb-1">Select Semester</label>
                <select wire:model="semester_id" id="semester_id" 
                    class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">-- Choose Semester --</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Exam --}}
            <div>
                <label for="exam_detail_id" class="block text-sm font-medium text-gray-700 mb-1">Select Exam: {{ $semester_id ? 'Sem:' . $semester_id : 'X' }} - {{ $myclass_id ? 'Class:' . $myclass_id : 'X' }}</label>
                <select wire:model="exam_detail_id" id="exam_detail_id" 
                    class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ empty($exams) ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                    {{ empty($exams) ? 'disabled' : '' }}>
                    <option value="">-- Choose Exam --</option> {{-- {{ $exams ? count($exams) : 0 }} --}}
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->id }}-{{ $exam->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Subject --}}
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Select Subject</label>
                <select wire:model="subject_id" id="subject_id" 
                    class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ empty($subjects) ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                    {{ empty($subjects) ? 'disabled' : '' }}>
                    <option value="">-- Choose Subject --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Students Table --}}
    @if($subject_id && count($students) > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">SL No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Student Name</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Roll No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Marks</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Absent?</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                            <tr wire:key="student-{{ $student->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $student->studentdb->student_name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $student->studentdb->student_code }}</div>
                                    <div class="text-xs text-gray-500">REG: {{ $student->studentdb->board_reg_no }}</div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $student->roll_no }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{-- {{ $student->studentdb->studentSubjects }} --}}
                                    @foreach($student->studentdb->studentSubjects as $sdbSubject)
                                    @php $selectedSubjectColor = $sdbSubject->subject->id == $subject_id ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-gray-100 text-gray-700 border-gray-200'; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $selectedSubjectColor }}">
                                        {{ $sdbSubject->subject->subject_code }}
                                    </span>
                                    @endforeach
                                </td>
                                
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="text" 
                                        wire:model.lazy="marks.{{ $student->id }}" 
                                        wire:change="saveMark({{ $student->id }})"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @if(isset($absent[$student->id]) && $absent[$student->id]) bg-gray-100 text-gray-500 @endif"
                                        {{ isset($absent[$student->id]) && $absent[$student->id] ? 'disabled' : '' }}
                                        placeholder="Marks"
                                    >
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                            wire:model="absent.{{ $student->id }}" 
                                            wire:change="toggleAbsent({{ $student->id }})"
                                            id="absent_{{ $student->id }}"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer transition duration-150 ease-in-out"
                                        >
                                        <label for="absent_{{ $student->id }}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer">
                                            {{ isset($absent[$student->id]) && $absent[$student->id] ? 'AB' : 'No' }}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($subject_id)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        No students found for the selected subject and class.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>


