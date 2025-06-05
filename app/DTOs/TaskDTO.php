<?php

namespace App\DTOs;

use App\Enums\TaskStatus;

class TaskDTO
{
    // Data Transfer Object for task data
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly int $priority,
        public readonly TaskStatus $status,
        public readonly ?int $parent_id = null,
        public readonly ?\DateTime $created_at = null,
        public readonly ?\DateTime $completed_at = null
    ) {
    }
}