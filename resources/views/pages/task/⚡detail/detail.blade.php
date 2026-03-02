<div>
    <div class="flex flex-col gap-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between">

            <div>
                <flux:heading size="xl">
                    Tasks
                </flux:heading>

                <flux:text class="text-gray-500 mt-1">
                    Manage your tasks efficiently
                </flux:text>
            </div>

            <div>
                <flux:modal.trigger name="add-task">

                    <flux:button size="sm" variant="primary">
                        Create
                    </flux:button>
                </flux:modal.trigger>
            </div>

        </div>

        {{-- PROJECT DETAIL CARD --}}
        @if ($this->project)
            <div class="mt-6">
                <flux:card class="p-6">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <flux:heading size="lg">{{ $this->project->name }}</flux:heading>
                            <span class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($this->project->start_date)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($this->project->end_date)->format('d M Y') }}
                            </span>
                        </div>
                        <flux:text class="text-gray-600 mt-2">
                            {{ $this->project->description }}
                        </flux:text>
                    </div>
                </flux:card>
            </div>
        @endif

        {{-- TABLE --}}
        <div class="w-full">
            <flux:card>

                <flux:table :paginate="$this->tasks">
                    <flux:table.columns>
                        <flux:table.column>Order</flux:table.column>
                        <flux:table.column>Title</flux:table.column>
                        <flux:table.column>Description</flux:table.column>
                        <flux:table.column>Category</flux:table.column>
                        <flux:table.column>Story Point</flux:table.column>
                        <flux:table.column>Estimated Hour</flux:table.column>
                        <flux:table.column>Due Date</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>

                        @forelse ($this->tasks as $task)
                            <flux:table.row :key="$task->id">

                                {{-- ORDER --}}
                                <flux:table.cell class="font-semibold">
                                    {{ $task->order }}
                                </flux:table.cell>

                                {{-- TITLE --}}
                                <flux:table.cell class="font-semibold">
                                    {{ $task->title }}
                                </flux:table.cell>

                                {{-- DESCRIPTION (truncate) --}}
                                <flux:table.cell class="text-gray-600">
                                    {{ \Illuminate\Support\Str::limit($task->description, 50) }}
                                </flux:table.cell>

                                {{-- CATEGORY --}}
                                <flux:table.cell>
                                    <span>
                                        {{ $task->category->name }}
                                    </span>
                                </flux:table.cell>

                                {{-- STORY POINT --}}
                                <flux:table.cell>
                                    @php
                                        $sp = $task->story_point->value;
                                        if ($sp <= 3) {
                                            $color = 'bg-green-100 text-green-700';
                                        } elseif ($sp <= 8) {
                                            $color = 'bg-yellow-100 text-yellow-700';
                                        } else {
                                            $color = 'bg-red-100 text-red-700';
                                        }
                                    @endphp
                                    <span class="px-2 py-1 rounded-full {{ $color }}">
                                        {{ $task->story_point->value }} ({{ $task->story_point->name }})
                                    </span>
                                </flux:table.cell>

                                {{-- ESTIMATED HOUR --}}
                                <flux:table.cell>
                                    @php
                                        $eh = $task->estimated_hour->value;
                                        if ($eh <= 8) {
                                            $color = 'bg-blue-100 text-blue-700';
                                        } elseif ($eh <= 24) {
                                            $color = 'bg-amber-100 text-amber-700';
                                        } else {
                                            $color = 'bg-red-100 text-red-700';
                                        }
                                    @endphp
                                    <span class="px-2 py-1 rounded-full {{ $color }}">
                                        {{ $task->estimated_hour->value }} Hour ({{ $task->estimated_hour->name }})
                                    </span>
                                </flux:table.cell>

                                {{-- DUE DATE --}}
                                <flux:table.cell>
                                    @php
                                        $due = \Carbon\Carbon::parse($task->due_date);
                                        $is_urgent = $due->isToday() || $due->isPast();
                                    @endphp

                                    <span class="{{ $is_urgent ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $due->format('d M Y') }}
                                    </span>

                                </flux:table.cell>

                            </flux:table.row>

                        @empty

                            <flux:table.row>
                                <flux:table.cell colspan="6">
                                    <div class="flex flex-col items-center justify-center py-10 text-center">

                                        <div class="text-gray-400 text-lg font-semibold">
                                            No tasks available
                                        </div>

                                        <div class="text-sm text-gray-500 mt-1">
                                            You haven't created any tasks yet. <br> Start by adding a new task to manage
                                            your
                                            work effectively.
                                        </div>

                                        <div class="mt-5">
                                            <flux:modal.trigger name="add-task">
                                                <flux:button variant="primary">
                                                    Add New Task
                                                </flux:button>
                                            </flux:modal.trigger>
                                        </div>

                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse

                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>

    <flux:modal name="add-task" class="w-full sm:w-96 md:w-225 lg:w-250 xl:w-300">
        <div class="space-y-6">

            {{-- HEADER --}}
            <div>
                <flux:heading size="lg">Add New Task</flux:heading>
                <flux:text class="mt-2">Add your new task</flux:text>
            </div>

            {{-- FORM --}}
            <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

                {{-- TITLE --}}
                <flux:field class="col-span-2">
                    <flux:label>Title</flux:label>
                    <flux:input wire:model.defer="title" type="text" placeholder="Enter task title" />
                    <flux:error name="title" />
                </flux:field>

                {{-- DESCRIPTION --}}
                <flux:field class="col-span-2">
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model.defer="description" rows="3" placeholder="Describe the task..." />
                    <flux:error name="description" />
                </flux:field>

                {{-- CATEGORY --}}
                <flux:field>
                    <flux:label>Category</flux:label>
                    <flux:select wire:model.defer="category_id">
                        <option value="">Select Category</option>
                        @foreach ($this->categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                {{-- STORY POINT --}}
                <flux:field>
                    <flux:label>Complexity</flux:label>
                    <flux:select wire:model.defer="story_point_id">
                        <option value="">Select Complexity</option>
                        @foreach ($this->story_points as $point)
                            <option value="{{ $point->id }}">
                                {{ $point->value }} | ({{ $point->name }})
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="story_point_id" />
                </flux:field>

                {{-- ESTIMATED HOUR --}}
                <flux:field>
                    <flux:label>Estimated Duration</flux:label>
                    <flux:select wire:model.defer="estimated_hour_id">
                        <option value="">Select Duration</option>
                        @foreach ($this->estimated_hours as $hour)
                            <option value="{{ $hour->id }}">
                                {{ $hour->value }} jam | ({{ $hour->name }})
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="estimated_hour_id" />
                </flux:field>

                {{-- DUE DATE --}}
                <flux:field>
                    <flux:label>Due Date</flux:label>
                    <flux:input wire:model.defer="due_date" type="date" />
                    <flux:error name="due_date" />
                </flux:field>

                {{-- ACTION --}}
                <div class="col-span-2 flex items-center justify-end mt-4">
                    <flux:button type="submit" variant="primary">
                        Save Task
                    </flux:button>
                </div>

            </form>
        </div>
    </flux:modal>

</div>
