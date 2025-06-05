<?php

namespace App\Enums;

// Enum for task status
enum TaskStatus: string
{
    case TODO = 'todo';
    case DONE = 'done';
}