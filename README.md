# Todo List API

A professional RESTful API for managing tasks with nested subtasks, built with Laravel 12, PHP 8.2, and MySQL. This API allows authenticated users to create, read, update, and delete tasks with full hierarchical support.

## Features

- **User Authentication**: Secure registration and login with Laravel Sanctum token-based authentication
- **Task Management**:
  - List tasks with advanced filtering (status, priority, full-text search)
  - Create tasks with optional nested subtasks
  - Update task properties and status
  - Delete tasks with authorization checks
- **Advanced Filtering & Sorting**:
  - Filter by `status` (todo/done), `priority` (1-5), and full-text search on title/description
  - Sort by `created_at`, `completed_at`, `priority` with multi-field sorting support
- **Business Logic Restrictions**:
  - Authorization: Users can only access/modify their own tasks
  - Cannot delete completed tasks
  - Cannot mark tasks complete if subtasks remain incomplete
- **Professional Architecture**:
  - Service layer pattern for business logic separation
  - Repository pattern for data abstraction
  - Data Transfer Objects (DTOs) for consistent data handling
  - Enums for type-safe status management
  - RESTful routing conventions
- **Production-Ready**:
  - Dockerized with MySQL and Nginx for containerized deployment
  - Comprehensive OpenAPI 3.0 specification
  - PSR-12 compliant code style (enforced by Laravel Pint)
  - Feature test coverage for critical operations

## Requirements

- **PHP**: 8.2 or higher
- **Composer**: Dependency manager
- **Docker**: Version 20.10+
- **Docker Compose**: Version 1.29+
- **MySQL**: 8.0 or higher (containerized)

## Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/taras723/todo-list-api.git
cd todo-list-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your settings (Docker defaults are pre-configured):
```env
APP_KEY=<generated-key>
DB_HOST=db
DB_DATABASE=todo_list
DB_USERNAME=root
DB_PASSWORD=secret
```

### 4. Start Docker Containers

```bash
docker-compose up -d
```

Verify containers are running:
```bash
docker-compose ps
```

### 5. Run Database Migrations & Seeders

```bash
docker-compose exec app php artisan migrate --seed
```

This creates:
- Database tables (users, tasks)
- Test user: `test@example.com` / `password`
- Sample tasks with nested subtasks

### 6. Verify Code Style

```bash
./vendor/bin/pint
```

## API Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/register` | Create new user account | ❌ |
| POST | `/api/login` | Authenticate and receive token | ❌ |
| GET | `/api/tasks` | List tasks (with filters/sorting) | ✅ Bearer Token |
| POST | `/api/tasks` | Create new task | ✅ Bearer Token |
| PUT | `/api/tasks/{id}` | Update existing task | ✅ Bearer Token |
| DELETE | `/api/tasks/{id}` | Delete task | ✅ Bearer Token |

### Authentication Examples

#### Register New User

```bash
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!"
  }'
```

#### Login

```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123!"
  }'
```

Response:
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

### Task Management Examples

#### List Tasks with Filters

```bash
curl -H "Authorization: Bearer <token>" \
  "http://localhost/api/tasks?status=todo&priority=3&search=urgent&sort_by=priority&sort_direction=desc"
```

Query Parameters:
- `status`: `todo` or `done`
- `priority`: 1-5
- `search`: Search in title/description
- `sort_by`: `created_at`, `completed_at`, or `priority`
- `sort_direction`: `asc` or `desc`

#### Create Task

```bash
curl -X POST -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Complete project",
    "description": "Finish the API implementation",
    "priority": 4,
    "status": "todo"
  }' http://localhost/api/tasks
```

#### Create Subtask

```bash
curl -X POST -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Write tests",
    "description": "Unit and feature tests",
    "priority": 3,
    "status": "todo",
    "parent_id": 1
  }' http://localhost/api/tasks
```

#### Update Task

```bash
curl -X PUT -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated title",
    "status": "done",
    "priority": 5
  }' http://localhost/api/tasks/1
```

#### Delete Task

```bash
curl -X DELETE -H "Authorization: Bearer <token>" \
  http://localhost/api/tasks/1
```

## Security & Authentication

**Laravel Sanctum** provides stateless API authentication:
- Tokens are issued upon successful login
- Include token in Authorization header: `Authorization: Bearer <token>`
- Tokens are tied to user accounts and can be revoked
- Default test user: `test@example.com` / `password`

## Database Schema

### Users Table
- `id`: Primary key
- `name`: User display name
- `email`: Unique email address
- `password`: Hashed password
- `email_verified_at`: Verification timestamp
- `timestamps`: Created/updated tracking

### Tasks Table
- `id`: Primary key
- `user_id`: Foreign key (task owner)
- `title`: Task title (indexed for search)
- `description`: Task description (full-text indexed)
- `status`: `todo` or `done` enum (indexed)
- `priority`: 1-5 integer (indexed)
- `parent_id`: Self-referencing for subtasks
- `completed_at`: Completion timestamp
- `timestamps`: Created/updated tracking

**Indexes**: `user_id`, `status`, `priority`, full-text on `title` and `description`

## Architecture

### Code Organization

```
app/
├── Models/              # Eloquent models (User, Task)
├── Http/
│   ├── Controllers/     # Request handlers
│   └── Requests/        # Form validation (StoreTaskRequest, UpdateTaskRequest)
├── Services/            # TaskService - business logic layer
├── Repositories/        # TaskRepository - data access layer
├── DTOs/                # TaskDTO - data transfer objects
└── Enums/               # TaskStatus - type-safe enums

routes/
└── api.php              # RESTful API routes with Sanctum middleware

tests/
├── Feature/             # Integration tests
└── Unit/                # Unit tests
```

### Design Patterns

- **Service Layer**: Encapsulates business logic
- **Repository Pattern**: Abstract data access
- **DTO Pattern**: Type-safe data transfer
- **Enum Pattern**: Type-safe status values

## API Documentation

Interactive documentation available via **OpenAPI 3.0** specification:

1. **View Specification**: `openapi.yaml` in repository root
2. **Import to Swagger UI**: https://editor.swagger.io
3. **Import to Postman**: Use OpenAPI URL in Postman import

## Testing

### Run All Tests

```bash
docker-compose exec app php artisan test
```

### Run Feature Tests Only

```bash
docker-compose exec app php artisan test --filter Feature
```

### Run with Coverage

```bash
docker-compose exec app php artisan test --coverage
```

## Code Style & Quality

The project adheres to **PSR-12** coding standards:

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style violations
./vendor/bin/pint
```

## Continuous Integration / Deployment

### GitHub Actions Workflow

The repository includes automated CI/CD pipeline:

- **Runs on**: Push to main, PRs to main
- **Tests**: PHPUnit test suite
- **Style Check**: Laravel Pint PSR-12 validation
- **Coverage**: Code coverage reporting

View workflow: `.github/workflows/ci.yml`

### Deployment Steps

1. **Pre-deployment**:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   ```

2. **Database**:
   ```bash
   php artisan migrate --force
   ```

3. **Start Services**:
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

## Troubleshooting

### Docker Logs

```bash
# Application logs
docker-compose logs app

# Nginx logs
docker-compose logs nginx

# MySQL logs
docker-compose logs db

# Follow logs in real-time
docker-compose logs -f app
```

### Database Connection Issues

```bash
# Verify MySQL is running
docker-compose ps

# Test connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### CSRF Token Issues

```bash
# Fetch CSRF cookie
curl -X GET http://localhost/sanctum/csrf-cookie

# Use with authentication requests
curl -b cookies.txt -c cookies.txt http://localhost/sanctum/csrf-cookie
```

### Clear Cache & Restart

```bash
# Clear all caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Restart containers
docker-compose restart
```

## Performance Optimization

- **Database Indexes**: Strategic indexes on frequently queried columns
- **Query Optimization**: Eager loading and select-specific columns
- **Caching**: Leverage Laravel's cache system for repeated queries
- **Alpine Linux**: Lightweight base image for reduced container size

## Production Checklist

- [ ] Update `.env` with production values
- [ ] Set `APP_DEBUG=false` and `APP_ENV=production`
- [ ] Configure secure database credentials
- [ ] Enable HTTPS/SSL certificates
- [ ] Set up database backups
- [ ] Configure monitoring and logging
- [ ] Run tests: `php artisan test`
- [ ] Check code style: `./vendor/bin/pint --test`
- [ ] Generate app key: `php artisan key:generate`

## License

MIT License - See LICENSE file for details

## Support

For issues, questions, or contributions:
- 📧 Open an issue on GitHub
- 🐛 Report bugs with reproduction steps
- 💡 Suggest features with use cases
