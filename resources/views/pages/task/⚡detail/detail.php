<?php

use App\Models\Category;
use App\Models\EstimatedHour;
use App\Models\Project;
use App\Models\StoryPoint;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $title;

    public $description;

    public $category_id;

    public $story_point_id;

    public $estimated_hour_id;

    public $due_date;

    public Project $project;

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    // =============================
    // COMPUTED: TASK LIST
    // =============================
    #[Computed()]
    public function tasks()
    {
        $tasks = Task::with([
            'user',
            'category',
            'story_point',
            'estimated_hour',
        ])
            ->where('project_id', $this->project->id)
            ->where('user_id', Auth::id())
            ->paginate();

        return $tasks;
    }

    // =============================
    // COMPUTED: CATEGORY LIST
    // =============================
    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    // =============================
    // COMPUTED: STORY POINT LIST
    // =============================
    #[Computed]
    public function story_points()
    {
        return StoryPoint::orderBy('value')->get();
    }

    // =============================
    // COMPUTED: ESTIMATED HOUR LIST
    // =============================
    #[Computed()]
    public function estimated_hours()
    {
        return EstimatedHour::orderBy('value')->get();
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'story_point_id' => 'required|exists:story_points,id',
        'estimated_hour_id' => 'required|exists:estimated_hours,id',
        'due_date' => 'required|date',
    ];

    protected $messages = [
        'title.required' => 'Please enter a title for the task.',
        'title.string' => 'The title must be a valid text.',
        'title.max' => 'The title cannot exceed 255 characters.',

        'description.required' => 'Please provide a description for the task.',
        'description.string' => 'The description must be valid text.',

        'category_id.required' => 'Please select a category.',
        'category_id.exists' => 'The selected category is invalid.',

        'story_point_id.required' => 'Please select a complexity level.',
        'story_point_id.exists' => 'The selected complexity level is invalid.',

        'estimated_hour_id.required' => 'Please select an estimated duration.',
        'estimated_hour_id.exists' => 'The selected duration is invalid.',

        'due_date.required' => 'Please select a due date.',
        'due_date.date' => 'The due date is not a valid date.',
    ];

    public function save()
    {
        // 1️⃣ VALIDATE INPUT
        $validated = $this->validate();

        // 2️⃣ CREATE TASK
        $task = \App\Models\Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'story_point_id' => $validated['story_point_id'],
            'estimated_hour_id' => $validated['estimated_hour_id'],
            'due_date' => $validated['due_date'],
            'user_id' => Auth::id(), // assign current user
            'project_id' => $this->project->id
        ]);

        // 3️⃣ RESET FORM
        $this->reset([
            'title',
            'description',
            'category_id',
            'story_point_id',
            'estimated_hour_id',
            'due_date',
        ]);
    }
};
