<?php

namespace App\Repositories;

use App\DTOs\TaskDTO;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getFilteredTasks(
        int $userId,
        ?string $status = null,
        ?int $priority = null,
        ?string $search = null,
        ?string $sortBy = null,
        ?string $sortDirection = 'asc',
        ?string $secondarySortBy = null,
        ?string $secondarySortDirection = 'asc'
    ): Collection {
        $query = Task::where('user_id', $userId)->with('subtasks');

        if ($status) {
            $query->where('status', $status);
        }
        if ($priority) {
            $query->where('priority', $priority);
        }
        if ($search) {
            $query->whereFullText(['title', 'description'], $search);
        }
        if ($sortBy) {
            $query->orderBy($sortBy, $sortDirection);
            if ($secondarySortBy && $secondarySortBy !== $sortBy) {
                $query->orderBy($secondarySortBy, $secondarySortDirection);
            }
        }

        return $query->get()->map(function ($task) {
            return $task->loadSubtasks();
        });
    }

    public function create(TaskDTO $dto, int $userId): Task
    {
        return Task::create([
            'user_id' => $userId,
            'parent_id' => $dto->parent_id,
            'status' => $dto->status,
            'priority' => $dto->priority,
            'title' => $dto->title,
            'description' => $dto->description,
        ]);
    }

    public function update(Task $task, TaskDTO $dto): Task
    {
        $task->update([
            'title' => $dto->title,
            'description' => $dto->description,
            'priority' => $dto->priority,
            'status' => $dto->status,
            'parent_id' => $dto->parent_id,
            'completed_at' => $dto->status === TaskStatus::DONE ? now() : null,
        ]);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}