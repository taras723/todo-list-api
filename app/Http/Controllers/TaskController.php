<?php

namespace App\Http\Controllers;

use App\DTOs\TaskDTO;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $service)
    {
    }

    // List tasks with filters and sorting
    public function index(): JsonResponse
    {
        $tasks = $this->service->getTasks(
            userId: auth()->id(),
            status: request()->query('status'),
            priority: request()->query('priority'),
            search: request()->query('search'),
            sortBy: request()->query('sort_by', 'created_at'),
            sortDirection: request()->query('sort_direction', 'asc')
        );

        return response()->json($tasks);
    }

    // Create a new task
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $dto = new TaskDTO(
            title: $request->input('title'),
            description: $request->input('description'),
            priority: $request->input('priority'),
            status: TaskStatus::from($request->input('status')),
            parent_id: $request->input('parent_id')
        );

        $task = $this->service->createTask($dto, auth()->id());
        return response()->json($task, 201);
    }

    // Update an existing task
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $dto = new TaskDTO(
            title: $request->input('title'),
            description: $request->input('description'),
            priority: $request->input('priority'),
            status: TaskStatus::from($request->input('status')),
            parent_id: $request->input('parent_id')
        );

        $task = $this->service->updateTask($task, $dto, auth()->id());
        return response()->json($task);
    }

    // Delete a task
    public function destroy(Task $task): JsonResponse
    {
        $this->service->deleteTask($task, auth()->id());
        return response()->json(null, 204);
    }
}