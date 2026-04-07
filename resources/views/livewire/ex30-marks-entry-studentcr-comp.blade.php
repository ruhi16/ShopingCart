<div>
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Student Marks Entry</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="selectedMyclassId" class="block text-sm font-medium text-gray-700 mb-1">Select Class</label>
                <select wire:model="selectedMyclassId" id="selectedMyclassId" 
                    class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">-- Choose Class --</option>
                    @foreach($myclasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if($selectedMyclassId && count($studentcrs) > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($studentcrs as $studentcr)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $studentcr->roll_no }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $studentcr->studentdb->student_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $studentcr->studentdb->student_code ?? 'N/A' }}, Reg No: {{ $studentcr->studentdb->board_reg_no ?? 'N/A' }}</div>
                                    @foreach($studentcr->studentdb->studentSubjects as $studentSubject)
                                        {{-- <div class="text-xs text-gray-500">{{ $studentSubject->subject->subject_code ?? 'N/A' }}</div> --}}
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                            {{ $studentSubject->subject->subject_code ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $studentcr->currentSection->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button wire:click="openMarksEntry({{ $studentcr->id }})"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium">
                                        Marks Entry
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($selectedMyclassId)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
            <p class="text-sm text-blue-700">No students found for this class.</p>
        </div>
    @endif

    @if($selectedStudentcrId)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeMarksEntry">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-[98vw] max-h-[95vh] overflow-y-auto m-2">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Marks Entry - {{ $studentcrs->firstWhere('id', $selectedStudentcrId)->studentdb->student_name ?? 'Student' }}
                            (Roll: {{ $studentcrs->firstWhere('id', $selectedStudentcrId)->roll_no ?? 'N/A' }})
                        </h3>
                        <button wire:click="closeMarksEntry" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    </div>

                    @if(count($examDetails) > 0 && count($studentSubjects) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase w-40 sticky left-0 bg-gray-50">Subject</th>
                                        @foreach($examDetails as $exam)
                                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase min-w-[120px]">
                                                {{ $exam->name ?? 'Exam ' . $exam->id }}
                                                <div class="text-xs text-gray-400 font-normal">
                                                    {{ $exam->semester->name ?? '' }}
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($studentSubjects as $studentSubject)
                                        @php
                                            $subjectHasSetting = false;
                                            foreach($examDetails as $exam) {
                                                $setting = $this->getExamSetting($exam->id, $studentSubject->subject_id);
                                                if($setting) {
                                                    $subjectHasSetting = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($subjectHasSetting)
                                            <tr>
                                                <td class="px-2 py-2 text-sm font-medium text-gray-900 whitespace-nowrap sticky left-0 bg-white">
                                                    {{ $studentSubject->subject->name ?? 'N/A' }}
                                                    <div class="text-xs text-gray-500">{{ $studentSubject->subject->subject_code ?? '' }}</div>
                                                </td>
                                                @foreach($examDetails as $exam)
                                                    @php
                                                        $setting = $this->getExamSetting($exam->id, $studentSubject->subject_id);
                                                        $existingMark = $this->getExistingMark($exam->id, $studentSubject->subject_id);
                                                    @endphp
                                                    <td class="px-2 py-2 text-center">
                                                        @if($setting)
                                                            <div class="text-xs text-gray-500 mb-1">
                                                                Full: {{ $setting->full_mark }}
                                                            </div>
                                                            <input type="number" 
                                                                wire:change="saveMark({{ $exam->id }}, {{ $studentSubject->subject_id }}, $event.target.value, false)"
                                                                value="{{ $existingMark['marks_obtained'] }}"
                                                                class="w-16 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-2 py-1"
                                                                min="0"
                                                                max="{{ $setting->full_mark }}"
                                                                placeholder="0"
                                                            >
                                                            <div class="mt-1">
                                                                <label class="inline-flex items-center text-xs text-gray-600">
                                                                    <input type="checkbox" 
                                                                        wire:change="saveMark({{ $exam->id }}, {{ $studentSubject->subject_id }}, null, $event.target.checked)"
                                                                        @if($existingMark['is_absent']) checked @endif
                                                                        class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                                    >
                                                                    <span class="ml-1">AB</span>
                                                                </label>
                                                            </div>
                                                        @else
                                                            <span class="text-xs text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-sm text-yellow-700">
                                No exam settings found. Please configure exam settings first.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>