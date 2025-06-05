# Todo List API

A RESTful API for managing tasks with nested subtasks, built with Laravel 11, PHP 8.1, and MySQL.

## Requirements
- PHP 8.1+
- Composer
- Docker and Docker Compose
- MySQL 8.0

## Setup
1. Clone the repository:
   ```bash
   git clone <your-repo-url>
   cd todo-list-api
2. Install dependencies:
   composer install
3. Copy .env.example to .env and configure:
   cp .env.example .env
   php artisan key:generate
4. Start Docker containers:
   docker-compose up -d
5. Run migrations and seeders:
   docker-compose exec app php artisan migrate --seed
6. Ensure code style with Pint:
   ./vendor/bin/pint

## API Endpoints
    GET /api/tasks: List tasks with filters (status, priority, search) and sorting (sort_by, sort_direction).
    POST /api/tasks: Create a task.
    PUT /api/tasks/{id}: Update a task.
    DELETE /api/tasks/{id}: Delete a task.
