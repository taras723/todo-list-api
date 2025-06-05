<?php

namespace App\Services;

use App\DTOs\TaskDTO;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    // Get filtered and sorted tasks
    public function getTasks(
        int $userId,
        ?string $status,
        ?int $priority,
        ?string $search,
        ?string $sortBy,
        ?string $sortDirection
    ): array {
        return $this->repository->getFilteredTasks($userId, $status, $priority, $search, $sortBy, $sortDirection)
            ->toArray();
    }

    // Create a new task
    public function createTask(TaskDTO $dto, int $userId): Task
    {
        if ($dto->parent_id && !$this->canHaveSubtask($dto->parent_id, $userId)) {
            throw ValidationException::withMessages(['parent_id' => 'Invalid or unauthorized parent task']);
        }

        return $this->repository->create($dto, $userId);
    }

    // Update an existing task
    public function updateTask(Task $task, TaskDTO $dto, int $userId): Task
    {
        if ($task->user_id !== $userId) {
            throw ValidationException::withMessages(['task' => 'Unauthorized to edit this task']);
        }

        if ($dto->status === TaskStatus::DONE && $this->hasIncompleteSubtasks($task)) {
            throw ValidationException::withMessages(['status' => 'Cannot complete task with incomplete subtasks']);
        }

        return $this->repository->update($task, $dto);
    }

    // Delete a task
    public function deleteTask(Task $task, int $userId): void
    {
        if ($task->user_id !== $userId) {
            throw ValidationException::withMessages(['task' => 'Unauthorized to delete this task']);
        }

        if ($task->status === TaskStatus::DONE) {
            throw ValidationException::withMessages(['task' => 'Cannot delete a completed task']);
        }

        $this->repository->delete($task);
    }

    // Check if a task has incomplete subtasks
    private function hasIncompleteSubtasks(Task $task): bool
    {
        return $task->subtasks()->where('status', TaskStatus::TODO)->exists();
    }

    // Validate if a parent task can have subtasks
    private function canHaveSubtask(?int $parentId, int $userId): bool
    {
        if (!$parentId) {
            return true;
        }

        $parent = Task::findOrFail($parentId);
        return $parent->user_id === $userId && $parent->status !== TaskStatus::DONE;
    }
}