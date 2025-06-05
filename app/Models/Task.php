<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    // Define fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'parent_id',
        'status',
        'priority',
        'title',
        'description',
        'created_at',
        'completed_at',
    ];

    // Cast attributes to specific types
    protected $casts = [
        'status' => TaskStatus::class,
        'created_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationship to the user who owns the task
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to the parent task
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // Relationship to subtasks
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }
}