# Todo List API

A RESTful API for managing tasks with nested subtasks, built with Laravel 10, PHP 8.1, and MySQL. This API allows authenticated users to create, read, update, and delete tasks, with support for filtering, sorting, and unlimited subtask nesting.

## Features
- **User Authentication**: Register and login to obtain a Sanctum token.
- **Task Management**:
  - List tasks with filters (status, priority, full-text search on title/description).
  - Create tasks with optional subtasks.
  - Update tasks (e.g., mark as completed).
  - Delete tasks (with restrictions).
- **Filtering and Sorting**:
  - Filter by `status` (todo/done), `priority` (1-5), and search (title/description).
  - Sort by `created_at`, `completed_at`, `priority`, with support for two-field sorting (e.g., priority desc, created_at asc).
- **Restrictions**:
  - Users cannot edit or delete others' tasks.
  - Cannot delete completed tasks.
  - Cannot mark tasks as completed if subtasks are incomplete.
- **Architecture**: Service layer, repository pattern, DTOs, Enums, RESTful routing.
- **Deployment**: Dockerized with MySQL and Nginx.
- **Documentation**: OpenAPI specification and PSR-12 compliant code.

## Requirements
- PHP 8.1+
- Composer
- Docker and Docker Compose
- MySQL 8.0

## Installation
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/todo-list-api.git
   cd todo-list-api
   ```
2. **Install Dependencies**:
   ```bash
   composer install
   ```
3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your database settings if needed (default uses Dockerized MySQL).
4. **Start Docker Containers**:
   ```bash
   docker-compose up -d
   ```
5. **Run Migrations and Seeders**:
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```
6. **Ensure Code Style**:
   ```bash
   ./vendor/bin/pint
   ```

## API Endpoints
| Method | Endpoint              | Description                          | Authentication |
|--------|-----------------------|--------------------------------------|----------------|
| POST   | `/api/register`       | Register a new user                  | None           |
| POST   | `/api/login`          | Authenticate and get token           | None           |
| GET    | `/api/tasks`          | List tasks with filters and sorting  | Bearer Token   |
| POST   | `/api/tasks`          | Create a task                        | Bearer Token   |
| PUT    | `/api/tasks/{id}`     | Update a task                        | Bearer Token   |
| DELETE | `/api/tasks/{id}`     | Delete a task                        | Bearer Token   |

### Example Requests
1. **Register**:
   ```bash
   curl -X POST http://localhost/api/register -H "Content-Type: application/json" -d '{"name":"Test User","email":"user@example.com","password":"password123","password_confirmation":"password123"}'
   ```
2. **Login**:
   ```bash
   curl -X POST http://localhost/api/login -H "Content-Type: application/json" -d '{"email":"user@example.com","password":"password123"}'
   ```
3. **List Tasks** (with filters and sorting):
   ```bash
   curl -H "Authorization: Bearer <token>" "http://localhost/api/tasks?status=todo&priority=3&search=test&sort_by=created_at&sort_direction=asc&secondary_sort_by=priority&secondary_sort_direction=desc"
   ```
4. **Create Task**:
   ```bash
   curl -X POST -H "Authorization: Bearer <token>" -H "Content-Type: application/json" -d '{"title":"New Task","description":"Description","priority":3,"status":"todo"}' http://localhost/api/tasks
   ```
5. **Create Subtask**:
   ```bash
   curl -X POST -H "Authorization: Bearer <token>" -H "Content-Type: application/json" -d '{"title":"Subtask","description":"Subtask Description","priority":2,"status":"todo","parent_id":1}' http://localhost/api/tasks
   ```
6. **Update Task**:
   ```bash
   curl -X PUT -H "Authorization: Bearer <token>" -H "Content-Type: application/json" -d '{"title":"Updated Task","description":"Updated","priority":4,"status":"done"}' http://localhost/api/tasks/1
   ```
7. **Delete Task**:
   ```bash
   curl -X DELETE -H "Authorization: Bearer <token>" http://localhost/api/tasks/1
   ```

## Authentication
- **Laravel Sanctum**: Obtain a Bearer token via `/api/register` or `/api/login`.
- **Default Seeded User**: `email: test@example.com`, `password: password`.

## OpenAPI Documentation
- The API is documented in `openapi.yaml`.
- Import into Swagger UI or Postman for interactive exploration.

## Database
- **Tables**: `users`, `tasks`.
- **Indexes**: `status`, `priority`, `user_id`, full-text on `title` and `description`.
- **Seeders**: Populates a test user and sample tasks with nested subtasks.

## Architecture
- **Models**: `User`, `Task` with recursive relationships for subtasks.
- **Repositories**: `TaskRepository` for database operations.
- **Services**: `TaskService` for business logic.
- **DTOs**: `TaskDTO` for data transfer.
- **Enums**: `TaskStatus` for task status.
- **Routing**: RESTful routes in `routes/api.php` with Sanctum middleware.
- **Validation**: Form requests (`StoreTaskRequest`, `UpdateTaskRequest`, `RegisterRequest`).

## Code Style
- Adheres to **PSR-12**, enforced by Laravel Pint.

## Testing
- Run feature tests for authentication and task operations:
  ```bash
  docker-compose exec app php artisan test
  ```

## Deployment
1. **Push to GitHub**:
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/your-username/todo-list-api.git
   git push -u origin main
   ```
   Ensure `.env` is excluded via `.gitignore`.
2. **Production Setup**:
   - Update `.env` (`APP_ENV=production`, database credentials).
   - Deploy Docker containers on a server with Docker Compose.

## Troubleshooting
- **Logs**:
  ```bash
  docker-compose logs app
  docker-compose logs nginx
  ```
- **CSRF for SPA**: Fetch CSRF cookie before auth requests:
  ```bash
  curl -X GET http://localhost/sanctum/csrf-cookie
  ```
- **Database Connection**:
  Verify `DB_HOST=db` in `.env` and ensure MySQL is running:
  ```bash
  docker-compose ps
  ```

## License
MIT