<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Seed the database with sample data
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create 10 top-level tasks
        Task::factory()->count(10)->create([
            'user_id' => $user->id,
            'status' => 'todo',
        ]);

        // Create 5 subtasks for the first task
        Task::factory()->count(5)->create([
            'user_id' => $user->id,
            'parent_id' => Task::first()->id,
            'status' => 'todo',
        ]);
    }
}