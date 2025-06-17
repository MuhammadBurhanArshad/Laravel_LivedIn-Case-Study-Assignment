# Laravel Task Manager API

A simple RESTful API built with Laravel for managing tasks with user authentication.

## Features

- User Registration & Login
- Token-based Authentication using Laravel Sanctum
- CRUD Operations for Tasks
- Input Validation & Error Handling
- API Responses in a Standardized Format

---

## Tech Stack

- Laravel 12
- PHP 8.2
- Sanctum (for API authentication)
- MySQL

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/MuhammadBurhanArshad/Laravel_LivedIn-Case-Study-Assignment.git
cd Laravel_LivedIn-Case-Study-Assignment
````

### 2. Install dependencies

```bash
composer install
```

### 3. Copy `.env` and set up database

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database settings in `.env`.

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Serve the application

```bash
php artisan serve
```

---

## Authentication

* Register: `POST /api/register`
* Login: `POST /api/login`
* Logout: `POST /api/logout` (Requires Bearer Token)

Add the token from login response as a Bearer token in the Authorization header for protected routes.

---

## API Endpoints

### Authentication

| Method | Endpoint        | Description         |
| ------ | --------------- | ------------------- |
| POST   | `/api/register` | Register a new user |
| POST   | `/api/login`    | Login and get token |
| POST   | `/api/logout`   | Logout the user     |

### Tasks

| Method | Endpoint          | Description            |
| ------ | ----------------- | ---------------------- |
| GET    | `/api/tasks`      | Get all tasks for user |
| POST   | `/api/tasks`      | Create a new task      |
| PUT    | `/api/tasks/{id}` | Update a task          |
| DELETE | `/api/tasks/{id}` | Delete a task          |

---

## Example Task Request

```json
POST /api/tasks
Authorization: Bearer {token}

{
  "name": "Finish Report",
  "description": "Complete the monthly report",
  "dueDate": "2025-06-20",
  "priority": "high",
  "status": "todo"
}
```

---

## Task Fields

* `name`: string (required)
* `description`: string (required)
* `dueDate`: date (required)
* `priority`: one of: `low`, `medium`, `high` (required)
* `status`: one of: `todo`, `in_progress`, `completed` (required)

---

## Response Format

```json
{
  "success": true,
  "isAllowed": true,
  "message": "Task created successfully",
  "task": {
    "id": 1,
    "name": "Finish Report",
    "description": "Complete the monthly report",
    "dueDate": "2025-06-20",
    "priority": "High",
    "status": "Todo",
    "created_at": "2025-06-17",
    "updatedAt": "2025-06-17"
  }
}
```

---

## License

This project is open-source and available under the [MIT License](LICENSE).
