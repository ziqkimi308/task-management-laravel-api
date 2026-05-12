# Laravel Task Management API

A complete RESTful task management API built with Laravel 12, featuring multi‑level relationships, API resources, form request validation, soft deletes, query scopes, and policies.

## 🚀 Features

- **User Authentication** – Register, login, logout, and user profile via Laravel Sanctum.
- **Multi‑level Relationships** – Users → Projects → Tasks with eager loading.
- **API Resources** – Clean and consistent JSON responses with conditional loading.
- **Form Request Validation** – Dedicated request classes with custom error messages.
- **Soft Deletes** – Trash, restore, and query deleted projects and tasks.
- **Query Scopes** – Reusable filters like `active()`, `completed()`, `overdue()`, `pending()`.
- **Accessors & Mutators** – Computed properties like `is_overdue`, auto‑set `completed_at`.
- **Advanced Filtering & Sorting** – Filter by status, priority, overdue, search, and combine filters.
- **Bulk Updates** – Update multiple tasks at once.
- **Dashboard Endpoint** – Aggregated statistics and recent items.
- **Authorization** – Policies to ensure users only access their own data.

## 🛠 Prerequisites

- PHP 8.1+
- Composer
- PostgreSQL
- Laravel 12

## 📌 API Endpoints

All routes are prefixed with `/api/v1`.

### Public Routes (no authentication required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `/register` | Register a new user |
| POST   | `/login`    | Login and obtain bearer token |

### Protected Routes (require `Authorization: Bearer <token>`)

#### User & Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `/logout` | Revoke current token |
| GET    | `/me`     | Get authenticated user’s profile |
| GET    | `/dashboard` | Aggregated project & task stats |

#### Projects

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | `/projects` | List user’s projects (with filters) |
| POST   | `/projects` | Create a new project |
| GET    | `/projects/{project}` | View a single project |
| PUT    | `/projects/{project}` | Update a project |
| DELETE | `/projects/{project}` | Soft‑delete a project |
| GET    | `/projects/trashed` | List trashed projects |
| POST   | `/projects/{project}/restore` | Restore a soft‑deleted project |

**Filtering:**  
`/projects?status=active&overdue=true&search=website&sort_by=deadline&sort_order=asc`

#### Tasks

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | `/tasks` | List user’s tasks (with filters) |
| POST   | `/tasks` | Create a new task |
| GET    | `/tasks/{task}` | View a single task |
| PUT    | `/tasks/{task}` | Update a task |
| DELETE | `/tasks/{task}` | Soft‑delete a task |
| PUT    | `/tasks/bulk` | Bulk update task statuses |

**Filtering:**  
`/tasks?project_id=1&status=in_progress&priority=urgent&overdue=true&high_priority=true&search=design`

## 🧪 Sample cURL Requests

**1. Login (get token)**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "john@example.com", "password": "password123"}'
```

**2. List projects**
```bash
curl http://localhost:8000/api/v1/projects \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**3. Create a project**
```bash
curl -X POST http://localhost:8000/api/v1/projects \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "API Documentation",
    "description": "Write comprehensive API docs",
    "color": "#F59E0B",
    "deadline": "2024-03-01"
  }'
```

**4. Get all tasks (with nested project & user)**
```bash
curl http://localhost:8000/api/v1/tasks \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**5. Filter tasks – completed and high priority**
```bash
curl "http://localhost:8000/api/v1/tasks?status=completed&priority=high" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**6. Create a task**
```bash
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "project_id": 1,
    "title": "Add payment integration",
    "priority": "urgent",
    "due_date": "2024-01-20"
  }'
```

**7. Bulk update task statuses**
```bash
curl -X PUT http://localhost:8000/api/v1/tasks/bulk \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"task_ids": [3,4,5], "status": "in_progress"}'
```

**8. Access dashboard**
```bash
curl http://localhost:8000/api/v1/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🧩 Key Laravel Concepts Used

| Feature | Implementation |
|---------|----------------|
| Multi‑level relationships | `hasMany`, `belongsTo`; eager loading with `with()` |
| API Resources | `UserResource`, `ProjectResource`, `TaskResource` with `whenLoaded()` |
| Form Requests | `StoreProjectRequest`, `UpdateProjectRequest`, etc. – separate validation logic |
| Soft Deletes | `SoftDeletes` trait; `onlyTrashed()`, `restore()` |
| Query Scopes | `scopeActive()`, `scopeCompleted()`, `scopeOverdue()`, etc. |
| Accessors | `getIsOverdueAttribute()` – auto‑appended to JSON |
| Model Events | `booted()` to set `completed_at` when status changes to completed |
| Authorization | Policies (`ProjectPolicy`, `TaskPolicy`) + `$this->authorize()` |
| Bulk Updates | `Task::whereIn()->update()` with user ownership check |
| Custom Validation | Hex colour & `after:today` date rules |

## 📦 Project Structure Highlights

```
laravel-task-manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── ProjectController.php
│   │   │   └── TaskController.php
│   │   ├── Requests/
│   │   │   ├── StoreProjectRequest.php
│   │   │   ├── UpdateProjectRequest.php
│   │   │   ├── StoreTaskRequest.php
│   │   │   └── UpdateTaskRequest.php
│   │   └── Resources/
│   │       ├── UserResource.php
│   │       ├── ProjectResource.php
│   │       └── TaskResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Project.php
│   │   └── Task.php
│   └── Policies/
│       ├── ProjectPolicy.php
│       └── TaskPolicy.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── ProjectSeeder.php
│       └── TaskSeeder.php
└── routes/
    └── api.php
```
