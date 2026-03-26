<?php

namespace App\Http\Livewire;

use App\Models\Bs07Subject;
use App\Models\Session;
use App\Models\School;
use App\Models\Bs03Category;
use Livewire\Component;
use Livewire\WithPagination;

class Bs07SubjectComp extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $subject_id = 0, $name = '', $description = '', $subject_code = '', $subject_type = '';
    public $category_id = 0;
    public $session_id = 0, $school_id = 0, $is_active = true, $remarks = '';
    public $isOpen = false;
    public $search = '';
    public $perPage = 10;

    // Options for dropdowns
    public $sessionOptions = [];
    public $schoolOptions = [];
    public $categoryOptions = [];
    public $subjectTypeOptions = ['Lab', 'Non-Lab'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'subject_code' => 'nullable|string|max:50',
            'subject_type' => 'nullable|string|max:50',
            'category_id' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'session_id' => 'required|integer|min:1',
            'school_id' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    public function mount()
    {
        $this->loadOptions();
    }

    public function render()
    {
        $subjects = Bs07Subject::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('subject_code', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'DESC')
            ->paginate($this->perPage);

        return view('livewire.bs07-subject-comp', [
            'subjects' => $subjects,
        ]);
    }

    public function loadOptions()
    {
        $this->sessionOptions = Session::orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
        $this->schoolOptions = School::orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
        $this->categoryOptions = Bs03Category::orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->loadOptions();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $subject = Bs07Subject::findOrFail($id);
        $this->subject_id = $id;
        $this->name = $subject->name;
        $this->description = $subject->description ?? '';
        $this->subject_code = $subject->subject_code ?? '';
        $this->subject_type = $subject->subject_type ?? '';
        $this->category_id = $subject->category_id ?? 0;
        $this->session_id = $subject->session_id;
        $this->school_id = $subject->school_id;
        $this->is_active = $subject->is_active;
        $this->remarks = $subject->remarks ?? '';
        
        $this->loadOptions();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->subject_id = 0;
        $this->name = '';
        $this->description = '';
        $this->subject_code = '';
        $this->subject_type = '';
        $this->category_id = 0;
        $this->session_id = 0;
        $this->school_id = 0;
        $this->is_active = true;
        $this->remarks = '';
    }

    public function store()
    {
        $validated = $this->validate();

        Bs07Subject::updateOrCreate(['id' => $this->subject_id], [
            'name' => $this->name,
            'description' => $this->description,
            'subject_code' => $this->subject_code,
            'subject_type' => $this->subject_type,
            'category_id' => $this->category_id ?: null,
            'session_id' => $this->session_id,
            'school_id' => $this->school_id,
            'is_active' => $this->is_active,
            'remarks' => $this->remarks,
        ]);

        session()->flash('message', $this->subject_id ? 'Subject updated successfully.' : 'Subject created successfully.');

        $this->closeModal();
    }

    public function delete($id)
    {
        Bs07Subject::find($id)->delete();
        session()->flash('message', 'Subject deleted successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
