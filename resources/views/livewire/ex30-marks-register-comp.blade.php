<div>
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <option value="1">School 1</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                <select wire:model="selected_myclass_id" class="w-full rounded border-gray-300">
                    <option value="">-- All Classes --</option>
                    @foreach($myclassOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Student</label>
                <input type="text" wire:model="search" placeholder="Search by name..."
                    class="w-full rounded border-gray-300">
            </div>
        </div>

        {{-- View Mode Toggle --}}
        <div class="flex justify-end mt-4">
            <div class="inline-flex rounded-md shadow-sm">
                <button wire:click="setViewMode('compact')"
                    class="px-4 py-2 text-sm font-medium rounded-l-md transition
                    {{ $viewMode == 'compact' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border' }}">
                    Compact View
                </button>
                <button wire:click="setViewMode('tabular')"
                    class="px-4 py-2 text-sm font-medium rounded-r-md transition
                    {{ $viewMode == 'tabular' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border' }}">
                    Tabular View
                </button>
            </div>
        </div>
    </div>

    {{-- Compact View --}}
    @if($viewMode == 'compact')
        <div class="space-y-6">
            @forelse($groupedData as $key => $data)
                @php
                    $myclass = App\Models\Bs04Myclass::find($data['myclass_id']);
                    $semester = App\Models\Bs05Semester::find($data['semester_id']);
                    $marksByStudent = $data['marks_by_student'] ?? [];
                @endphp
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3">
                        <h3 class="text-lg font-bold text-white">
                            {{ $myclass->name ?? 'N/A' }} - {{ $semester->name ?? 'N/A' }}
                        </h3>
                        <p class="text-sm text-indigo-100">
                            {{ count($data['students']) }} Students
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-12">Roll</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student Name
                                    </th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">%</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Grade</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Pass/Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($data['students'] as $studentcr)
                                            @php
                                                $studentMarks = $marksByStudent[$studentcr->id] ?? null;
                                            @endphp
                                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                                <td class="px-3 py-2 font-medium text-gray-900">{{ $studentcr->roll_no }}</td>
                                                <td class="px-3 py-2 font-medium text-gray-900">
                                                    {{ $studentcr->studentdb->student_name ?? 'N/A' }}
                                                </td>
                                                <td class="px-3 py-2 text-center font-bold text-indigo-600">
                                                    {{ $studentMarks['totalObtained'] ?? 0 }} / {{ $studentMarks['totalFullMark'] ?? 0 }}
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <span
                                                        class="font-bold {{ ($studentMarks['overallPercentage'] ?? 0) >= 40 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $studentMarks['overallPercentage'] ?? 0 }}%
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <span
                                                        class="px-2 py-1 rounded-full text-xs font-bold
                                                                    {{ ($studentMarks['overallGrade'] ?? 'N') == 'A+' || ($studentMarks['overallGrade'] ?? 'N') == 'A' ? 'bg-green-100 text-green-800' :
                                    (($studentMarks['overallGrade'] ?? 'N') == 'F' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                        {{ $studentMarks['overallGrade'] ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-center text-sm">
                                                    <span
                                                        class="{{ ($studentMarks['subjectsPassed'] ?? 0) == ($studentMarks['totalSubjects'] ?? 0) ? 'text-green-600' : 'text-orange-600' }}">
                                                        {{ $studentMarks['subjectsPassed'] ?? 0 }}/{{ $studentMarks['totalSubjects'] ?? 0 }}
                                                    </span>
                                                </td>
                                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                    No student records found.
                </div>
            @endforelse
        </div>
    @endif

    {{-- Tabular View --}}
    @if($viewMode == 'tabular')
        <div class="space-y-6">
            @forelse($groupedData as $key => $data)
                @php
                    $myclass = App\Models\Bs04Myclass::find($data['myclass_id']);
                    $semester = App\Models\Bs05Semester::find($data['semester_id']);
                    $marksByStudent = $data['marks_by_student'] ?? [];

                    // Get all unique subjects for this myclass/semester
                    $allSubjects = [];
                    foreach ($marksByStudent as $studentId => $studentData) {
                        foreach ($studentData['marksData'] ?? [] as $settingId => $mark) {
                            if (!isset($allSubjects[$settingId])) {
                                $allSubjects[$settingId] = [
                                    'subject_id' => $mark['subject_id'],
                                    'subject_name' => $mark['subject_name'],
                                    'subject_code' => $mark['subject_code'],
                                    'full_mark' => $mark['full_mark'],
                                ];
                            }
                        }
                    }
                @endphp
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3">
                        <h3 class="text-lg font-bold text-white">
                            {{ $myclass->name ?? 'N/A' }} - {{ $semester->name ?? 'N/A' }}
                        </h3>
                        <p class="text-sm text-indigo-100">
                            {{ count($data['students']) }} Students | {{ count($allSubjects) }} Subjects
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 bg-gray-50">
                                        Roll</th>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase sticky left-12 bg-gray-50">
                                        Student Name</th>
                                    @foreach($allSubjects as $settingId => $subject)
                                        <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase min-w-[80px]">
                                            <div>{{ $subject['subject_code'] ?: $subject['subject_name'] }}</div>
                                            <div class="text-xs text-gray-400 font-normal">FM:{{ $subject['full_mark'] }}</div>
                                        </th>
                                    @endforeach
                                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase bg-gray-100">
                                        Total</th>
                                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase bg-gray-100">%
                                    </th>
                                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase bg-gray-100">
                                        Grade</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($data['students'] as $studentcr)
                                            @php
                                                $studentMarks = $marksByStudent[$studentcr->id] ?? null;
                                            @endphp
                                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                                <td class="px-2 py-2 font-medium text-gray-900 sticky left-0 bg-inherit">
                                                    {{ $studentcr->roll_no }}</td>
                                                <td
                                                    class="px-2 py-2 font-medium text-gray-900 sticky left-12 bg-inherit max-w-[150px] truncate">
                                                    {{ $studentcr->studentdb->student_name ?? 'N/A' }}
                                                </td>
                                                @foreach($allSubjects as $settingId => $subject)
                                                    @php
                                                        $mark = $studentMarks['marksData'][$settingId] ?? null;
                                                    @endphp
                                                    <td class="px-2 py-2 text-center">
                                                        @if($mark && $mark['marks_obtained'] !== null)
                                                            <span
                                                                class="font-medium {{ $mark['marks_obtained'] >= $mark['pass_mark'] ? 'text-green-600' : 'text-red-600' }}">
                                                                {{ $mark['marks_obtained'] }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="px-2 py-2 text-center font-bold bg-gray-50">
                                                    {{ $studentMarks['totalObtained'] ?? 0 }} / {{ $studentMarks['totalFullMark'] ?? 0 }}
                                                </td>
                                                <td class="px-2 py-2 text-center bg-gray-50">
                                                    <span
                                                        class="font-bold {{ ($studentMarks['overallPercentage'] ?? 0) >= 40 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $studentMarks['overallPercentage'] ?? 0 }}%
                                                    </span>
                                                </td>
                                                <td class="px-2 py-2 text-center bg-gray-50">
                                                    <span
                                                        class="px-2 py-1 rounded-full text-xs font-bold
                                                                    {{ ($studentMarks['overallGrade'] ?? 'N') == 'A+' || ($studentMarks['overallGrade'] ?? 'N') == 'A' ? 'bg-green-100 text-green-800' :
                                    (($studentMarks['overallGrade'] ?? 'N') == 'F' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                        {{ $studentMarks['overallGrade'] ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                    No student records found.
                </div>
            @endforelse
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $studentcrs->links() }}
    </div>
</div>