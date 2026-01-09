# Folia Backend Challenge - Reminder API

This project is a RESTful API for managing recurring reminders built with Laravel 11. The API supports creating, reading, updating, and deleting reminders with various recurrence patterns, as well as searching and querying reminders by date range.

## Technologies Used

- Laravel 11.0
- PHP 8.2
- SQLite Database
- Docker & Docker Compose
- Eloquent ORM
- Carbon (DateTime handling)

## Setup Instructions

1. Clone this repository

2. Navigate to the laravel-starter directory:
```bash
cd laravel-starter
```

3. Start the Docker containers:
```bash
make up
```

4. Run database migrations:
```bash
docker compose exec -it app php artisan migrate
```

5. The API is now running at http://localhost:8080

## Implementation Process

### Step 1: Database Schema

Created a migration file to define the reminders table schema with the following columns:
- id (auto-incrementing primary key)
- title (required string)
- description (nullable text)
- recurrence_type (enum: none, daily, interval, weekly)
- recurrence_interval (nullable integer for interval-based recurrence)
- recurrence_days (nullable JSON array for weekly recurrence days)
- reminder_time (nullable time)
- start_date (required datetime)
- end_date (nullable datetime)
- timestamps (created_at, updated_at)

### Step 2: Reminder Model

Created the Reminder model with:
- Mass assignment protection using the fillable property
- Type casting for start_date and end_date to datetime objects
- Type casting for recurrence_days to array
- Custom getOccurrences method to calculate reminder occurrences within a date range

### Step 3: Recurrence Logic

Implemented the getOccurrences method in the Reminder model to handle four recurrence types:

1. None: One-time reminder on the start_date
2. Daily: Reminder occurs every day between start_date and end_date
3. Interval: Reminder occurs every N days (specified by recurrence_interval)
4. Weekly: Reminder occurs on specific days of the week (specified in recurrence_days array)

The method calculates the intersection between the requested date range and the reminder's validity period, then generates occurrence objects for each matching date.

### Step 4: API Controller

Created ReminderController with the following endpoints:

- index(): Retrieve all reminders
- show(id): Retrieve a single reminder by ID
- store(request): Create a new reminder
- update(request, id): Update an existing reminder
- destroy(id): Delete a reminder
- search(request): Search reminders by keyword in title or description
- occurrences(request): Get all reminder occurrences within a date range

### Step 5: API Routes

Configured routes in api.php with proper ordering to prevent route conflicts:
- GET /api/reminders
- GET /api/reminders/search
- GET /api/reminders/occurrences
- GET /api/reminders/{id}
- POST /api/reminders
- PUT /api/reminders/{id}
- DELETE /api/reminders/{id}

### Step 6: Input Validation

Added validation rules to ensure data integrity:

For store() and update() methods:
- title: required, string, max 255 characters
- description: optional string
- recurrence_type: required, must be one of: none, daily, interval, weekly
- recurrence_interval: optional integer, minimum 1
- recurrence_days: optional array of valid day names
- reminder_time: optional, must match H:i:s format
- start_date: required valid date
- end_date: optional valid date, must be after or equal to start_date

For occurrences() method:
- start_date: required valid date
- end_date: required valid date, must be after or equal to start_date

### Step 7: Testing

Tested all endpoints with various scenarios:
- CRUD operations for all recurrence types
- Validation error handling
- Search functionality with different keywords
- Date range queries with overlapping and non-overlapping periods
- Edge cases including non-existent IDs and invalid input data

## API Documentation

### Create Reminder
```bash
POST /api/reminders
Content-Type: application/json

{
  "title": "Take Vitamins",
  "description": "Daily vitamins",
  "recurrence_type": "daily",
  "start_date": "2026-01-10",
  "end_date": "2026-01-20",
  "reminder_time": "08:00:00"
}
```

### Get All Reminders
```bash
GET /api/reminders
```

### Get Single Reminder
```bash
GET /api/reminders/{id}
```

### Update Reminder
```bash
PUT /api/reminders/{id}
Content-Type: application/json

{
  "title": "Updated Title",
  "description": "Updated description",
  "recurrence_type": "none",
  "start_date": "2026-01-15"
}
```

### Delete Reminder
```bash
DELETE /api/reminders/{id}
```

### Search Reminders
```bash
GET /api/reminders/search?keyword=vitamins
```

### Get Occurrences in Date Range
```bash
GET /api/reminders/occurrences?start_date=2026-01-10&end_date=2026-01-20
```

## Testing

A comprehensive test suite is documented in TESTING.md, which includes:
- Test cases for all endpoints
- Expected outputs for each test
- Validation error scenarios
- Edge case coverage

To run the tests manually, use the curl commands provided in TESTING.md.

## Project Structure

- source/app/Models/Reminder.php: Domain model with recurrence calculation logic
- source/app/Controllers/API/ReminderController.php: HTTP request handlers
- source/routes/api.php: API route definitions
- source/database/migrations/: Database schema definitions
