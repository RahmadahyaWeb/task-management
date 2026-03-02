<?php

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $name;

    public $description;

    public $start_date;

    public $end_date;

    #[Computed()]
    public function projects()
    {
        $projects = Project::with([
            'user',
        ])
            ->where('user_id', Auth::id())
            ->paginate();

        return $projects;
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ];

    protected $messages = [
        'name.required' => 'Please enter a name for the task.',
        'name.string' => 'The name must be a valid text.',
        'name.max' => 'The name cannot exceed 255 characters.',

        'description.required' => 'Please provide a description for the task.',
        'description.string' => 'The description must be valid text.',

        'start_date.required' => 'Please select a start date.',
        'start_date.date' => 'The start date is not a valid date.',

        'end_date.required' => 'Please select a end date.',
        'end_date.date' => 'The end date is not a valid date.',
    ];

       public function save()
    {
        // 1️⃣ VALIDATE INPUT
        $validated = $this->validate();

        // 2️⃣ CREATE PROJECT
        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'user_id' => Auth::id()
        ]);

        // 3️⃣ RESET FORM
        $this->reset([
            'name',
            'description',
            'start_date',
            'end_date',
        ]);
    }
};
