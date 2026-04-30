# Task Management API – Laravel

A multi-feature task management REST API built with Laravel, featuring user authentication, full CRUD operations, and ownership-based access control.

## Features

- Token-based authentication with Laravel Sanctum
- Full CRUD operations for tasks
- Ownership-based access control (users manage only their own tasks)
- API resource classes for clean data transformation
- Form request validation
- Structured JSON responses
- Eloquent relationships between users and tasks

## Tech Stack

- PHP / Laravel
- Laravel Sanctum
- MySQL
- Eloquent ORM

## Installation

1. Clone the repository
   git clone https://github.com/ziqkimi308/task-management-api.git

2. Install dependencies
   composer install

3. Copy and configure environment file
   cp .env.example .env

4. Generate application key
   php artisan key:generate

5. Run migrations
   php artisan migrate

6. Start the server
   php artisan serve

## API Endpoints

| Method | Endpoint           | Auth Required | Description          |
|--------|--------------------|---------------|----------------------|
| POST   | /api/register      | No            | Register new user    |
| POST   | /api/login         | No            | Login user           |
| POST   | /api/logout        | Yes           | Logout user          |
| GET    | /api/tasks         | Yes           | Get all user tasks   |
| POST   | /api/tasks         | Yes           | Create new task      |
| GET    | /api/tasks/{id}    | Yes           | Get specific task    |
| PUT    | /api/tasks/{id}    | Yes           | Update specific task |
| DELETE | /api/tasks/{id}    | Yes           | Delete specific task |

## License
MIT
