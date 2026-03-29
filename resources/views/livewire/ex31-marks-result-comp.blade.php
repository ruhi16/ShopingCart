<div class="p-6">
    @if($student)
        {{-- Result Sheet Header --}}
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <div class="text-center border-b-2 border-gray-300 pb-4 mb-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">MARKS STATEMENT</h1>
                <p class="text-sm text-gray-600">Academic Session: {{ $student->session->name ?? 'N/A' }}</p>
            </div>

            {{-- Student Information --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Student Name:</p>
                    <p class="font-bold text-gray-800">{{ $student->studentdb->student_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Roll Number:</p>
                    <p class="font-bold text-gray-800">{{ $student->roll_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Class:</p>
                    <p class="font-bold text-gray-800">{{ $student->currentMyclass->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Section:</p>
                    <p class="font-bold text-gray-800">{{ $student->currentSection->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Semester:</p>
                    <p class="font-bold text-gray-800">{{ $student->currentSemester->name ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Marks Table --}}
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th rowspan="2" class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border border-gray-300">Subject</th>
                            <th colspan="{{ count($examDetails) }}" class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-300">Exam Details</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-300 bg-blue-50">Total</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-300 bg-green-50">Grade</th>
                        </tr>
                        <tr>
                            @foreach($examDetails as $examDetail)
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 border border-gray-300">
                                    <div class="font-bold">{{ Str::limit($examDetail->examName->name ?? 'Exam', 15) }}</div>
                                    <div class="text-xs">{{ $examDetail->examType->name ?? '' }}</div>
                                    <div class="text-xs">{{ $examDetail->examPart->name ?? '' }} • {{ $examDetail->examMode->name ?? '' }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marksData as $subjectId => $subjectData)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-semibold text-gray-800 border border-gray-300">
                                    {{ $subjectData['subject']->short_name ?? $subjectData['subject']->name ?? 'Subject' }}
                                </td>
                                
                                @foreach($examDetails as $examDetail)
                                    @php
                                        $examMark = $subjectData['exam_marks'][$examDetail->id] ?? null;
                                    @endphp
                                    <td class="px-3 py-3 text-center text-sm border border-gray-300 {{ $examMark && $examMark['is_absent'] ? 'bg-red-50' : '' }}">
                                        @if($examMark)
                                            @if($examMark['is_absent'])
                                                <span class="text-red-600 font-bold text-xs">ABSENT</span>
                                            @elseif($examMark['marks_obtained'] !== null)
                                                <div class="font-bold text-blue-800">{{ number_format($examMark['marks_obtained'], 2) }}</div>
                                                <div class="text-xs text-gray-500">/{{ $examMark['full_mark'] }}</div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @else
                                            <span class="text-gray-300">N/A</span>
                                        @endif
                                    </td>
                                @endforeach
                                
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-800 border border-gray-300 bg-blue-50">
                                    {{ number_format($subjectData['subject_total'], 2) }}/{{ $subjectData['subject_full'] }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold border border-gray-300 {{ $subjectData['subject_grade'] == 'F' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $subjectData['subject_grade'] }}
                                    <div class="text-xs text-gray-600">{{ $subjectData['subject_percentage'] }}%</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="{{ 1 + count($examDetails) }}" class="px-4 py-3 text-right text-sm text-gray-700 border border-gray-300">GRAND TOTAL:</td>
                            <td class="px-4 py-3 text-center text-lg text-blue-800 border border-gray-300 bg-blue-50">
                                {{ number_format($totalMarks, 2) }}/{{ $totalFullMarks }}
                            </td>
                            <td class="px-4 py-3 text-center text-lg {{ $grade == 'F' ? 'text-red-700 bg-red-100' : 'text-green-700 bg-green-100' }} border border-gray-300">
                                {{ $grade }}
                                <div class="text-sm">{{ $percentage }}%</div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Result Summary --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                    <div class="text-sm text-gray-600 mb-1">Overall Percentage</div>
                    <div class="text-2xl font-bold text-blue-800">{{ $percentage }}%</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border-2 border-green-200">
                    <div class="text-sm text-gray-600 mb-1">Final Grade</div>
                    <div class="text-2xl font-bold text-green-800">{{ $grade }}</div>
                </div>
                <div class="p-4 rounded-lg border-2 {{ $resultStatus == 'PASS' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <div class="text-sm text-gray-600 mb-1">Result Status</div>
                    <div class="text-2xl font-bold {{ $resultStatus == 'PASS' ? 'text-green-800' : 'text-red-800' }}">{{ $resultStatus }}</div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="border-t-2 border-gray-300 pt-4 mt-6">
                <div class="grid grid-cols-3 gap-4 text-center text-sm text-gray-600">
                    <div>
                        <p>Date of Issue:</p>
                        <p class="font-bold">{{ date('F d, Y') }}</p>
                    </div>
                    <div>
                        <p>Controller of Examinations</p>
                        <p class="text-xs text-gray-500">(Signature)</p>
                    </div>
                    <div>
                        <p>Principal</p>
                        <p class="text-xs text-gray-500">(Signature)</p>
                    </div>
                </div>
            </div>

            {{-- Print Button --}}
            <div class="mt-6 text-center">
                <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    📄 Print Result Sheet
                </button>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p class="text-gray-500 text-lg mb-2">No result data found</p>
            <p class="text-sm text-gray-400">Please ensure the URL contains valid session, class, and student ID parameters.</p>
        </div>
    @endif
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .p-6, .p-6 * {
            visibility: visible;
        }
        .p-6 {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button {
            display: none;
        }
    }
</style>
@endpush
