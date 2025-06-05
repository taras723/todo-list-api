<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 3,
            'status' => 'todo',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'user_id' => $user->id]);
    }
}