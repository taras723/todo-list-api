<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('auth_token')->plainTextToken;
    }

    public function test_user_can_list_tasks_with_filters_and_sorting(): void
    {
        Task::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::TODO,
            'priority' => 3,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/tasks?status=todo&priority=3&sort_by=created_at&sort_direction=asc&secondary_sort_by=priority&secondary_sort_direction=desc');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_user_can_create_task(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'priority' => 3,
                'status' => 'todo',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Task']);
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'user_id' => $this->user->id]);
    }

    public function test_user_can_create_subtask(): void
    {
        $parent = Task::factory()->create(['user_id' => $this->user->id]);
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/tasks', [
                'title' => 'Subtask',
                'description' => 'Subtask Description',
                'priority' => 2,
                'status' => 'todo',
                'parent_id' => $parent->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['parent_id' => $parent->id]);
    }

    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Task',
                'description' => 'Updated Description',
                'priority' => 4,
                'status' => 'todo',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Task']);
    }

    public function test_user_cannot_update_other_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Task',
                'priority' => 4,
                'status' => 'todo',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['task']);
    }

    public function test_user_cannot_mark_task_done_with_incomplete_subtasks(): void
    {
        $parent = Task::factory()->create(['user_id' => $this->user->id]);
        Task::factory()->create(['user_id' => $this->user->id, 'parent_id' => $parent->id, 'status' => TaskStatus::TODO]);
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/tasks/{$parent->id}", [
                'title' => $parent->title,
                'priority' => $parent->priority,
                'status' => 'done',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_completed_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::DONE,
            'completed_at' => now(),
        ]);
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['task']);
    }
}