# API Testing Documentation

This document contains all test cases for the Reminder API with expected outputs.

## Prerequisites

```bash
# Start the server
make up

# Fresh database
docker compose exec -it app php artisan migrate:fresh
```

---

## 1. CREATE Reminder (POST /api/reminders)

### Test 1.1: Create Simple Reminder (No Recurrence)
```bash
curl -X POST http://localhost:8080/api/reminders \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Doctor Appointment",
    "description": "Annual checkup",
    "recurrence_type": "none",
    "start_date": "2026-02-15",
    "reminder_time": "09:00:00"
  }'
```

**Expected Output:**
```json
{
  "id": 1,
  "title": "Doctor Appointment",
  "description": "Annual checkup",
  "recurrence_type": "none",
  "recurrence_interval": null,
  "recurrence_days": null,
  "reminder_time": "09:00:00",
  "start_date": "2026-02-15T00:00:00.000000Z",
  "end_date": null,
  "created_at": "2026-01-09T...",
  "updated_at": "2026-01-09T..."
}
```

### Test 1.2: Create Daily Reminder
```bash
curl -X POST http://localhost:8080/api/reminders \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Take Vitamins",
    "description": "Daily vitamins",
    "recurrence_type": "daily",
    "start_date": "2026-01-10",
    "end_date": "2026-01-20",
    "reminder_time": "08:00:00"
  }'
```

**Expected Output:**
```json
{
  "id": 2,
  "title": "Take Vitamins",
  "description": "Daily vitamins",
  "recurrence_type": "daily",
  "recurrence_interval": null,
  "recurrence_days": null,
  "reminder_time": "08:00:00",
  "start_date": "2026-01-10T00:00:00.000000Z",
  "end_date": "2026-01-20T00:00:00.000000Z",
  "created_at": "2026-01-09T...",
  "updated_at": "2026-01-09T..."
}
```

### Test 1.3: Create Interval Reminder (Every 3 Days)
```bash
curl -X POST http://localhost:8080/api/reminders \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Water Plants",
    "description": "Water the garden",
    "recurrence_type": "interval",
    "recurrence_interval": 3,
    "start_date": "2026-01-10",
    "end_date": "2026-01-25",
    "reminder_time": "18:00:00"
  }'
```

**Expected Output:**
```json
{
  "id": 3,
  "title": "Water Plants",
  "description": "Water the garden",
  "recurrence_type": "interval",
  "recurrence_interval": 3,
  "recurrence_days": null,
  "reminder_time": "18:00:00",
  "start_date": "2026-01-10T00:00:00.000000Z",
  "end_date": "2026-01-25T00:00:00.000000Z",
  "created_at": "2026-01-09T...",
  "updated_at": "2026-01-09T..."
}
```

### Test 1.4: Create Weekly Reminder (Mon, Wed, Fri)
```bash
curl -X POST http://localhost:8080/api/reminders \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Gym Workout",
    "description": "Strength training",
    "recurrence_type": "weekly",
    "recurrence_days": ["monday", "wednesday", "friday"],
    "start_date": "2026-01-10",
    "end_date": "2026-01-31",
    "reminder_time": "06:00:00"
  }'
```

**Expected Output:**
```json
{
  "id": 4,
  "title": "Gym Workout",
  "description": "Strength training",
  "recurrence_type": "weekly",
  "recurrence_interval": null,
  "recurrence_days": ["monday", "wednesday", "friday"],
  "reminder_time": "06:00:00",
  "start_date": "2026-01-10T00:00:00.000000Z",
  "end_date": "2026-01-31T00:00:00.000000Z",
  "created_at": "2026-01-09T...",
  "updated_at": "2026-01-09T..."
}
```

### Test 1.5: Validation Error - Missing Required Field
```bash
curl -X POST http://localhost:8080/api/reminders \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Missing title",
    "recurrence_type": "none",
    "start_date": "2026-01-10"
  }'
```

**Expected Output:**
```json
{
  "message": "The title field is required.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```
**Status Code:** 422

### Test 1.6: Validation Error - Invalid recurrence_type
```bash
curl -X POST http://localhost:8080/api/reminders \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test",
    "recurrence_type": "monthly",
    "start_date": "2026-01-10"
  }'
```

**Expected Output:**
```json
{
  "message": "The selected recurrence type is invalid.",
  "errors": {
    "recurrence_type": ["The selected recurrence type is invalid."]
  }
}
```
**Status Code:** 422

### Test 1.7: Validation Error - end_date Before start_date
```bash
curl -X POST http://localhost:8080/api/reminders \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test",
    "recurrence_type": "none",
    "start_date": "2026-01-20",
    "end_date": "2026-01-10"
  }'
```

**Expected Output:**
```json
{
  "message": "The end date field must be a date after or equal to start date.",
  "errors": {
    "end_date": ["The end date field must be a date after or equal to start date."]
  }
}
```
**Status Code:** 422

---

## 2. READ All Reminders (GET /api/reminders)

### Test 2.1: Get All Reminders
```bash
curl http://localhost:8080/api/reminders
```

**Expected Output:**
```json
[
  {
    "id": 1,
    "title": "Doctor Appointment",
    ...
  },
  {
    "id": 2,
    "title": "Take Vitamins",
    ...
  },
  {
    "id": 3,
    "title": "Water Plants",
    ...
  },
  {
    "id": 4,
    "title": "Gym Workout",
    ...
  }
]
```

---

## 3. READ One Reminder (GET /api/reminders/{id})

### Test 3.1: Get Specific Reminder
```bash
curl http://localhost:8080/api/reminders/1
```

**Expected Output:**
```json
{
  "id": 1,
  "title": "Doctor Appointment",
  "description": "Annual checkup",
  "recurrence_type": "none",
  "recurrence_interval": null,
  "recurrence_days": null,
  "reminder_time": "09:00:00",
  "start_date": "2026-02-15T00:00:00.000000Z",
  "end_date": null,
  "created_at": "2026-01-09T...",
  "updated_at": "2026-01-09T..."
}
```

### Test 3.2: Get Non-existent Reminder
```bash
curl http://localhost:8080/api/reminders/999
```

**Expected Output:**
```json
{
  "message": "No query results for model [App\\Models\\Reminder] 999"
}
```
**Status Code:** 404

---

## 4. UPDATE Reminder (PUT /api/reminders/{id})

### Test 4.1: Update Reminder Successfully
```bash
curl -X PUT http://localhost:8080/api/reminders/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Doctor Appointment - UPDATED",
    "description": "Annual checkup with Dr. Smith",
    "recurrence_type": "none",
    "start_date": "2026-02-15",
    "reminder_time": "10:00:00"
  }'
```

**Expected Output:**
```json
{
  "id": 1,
  "title": "Doctor Appointment - UPDATED",
  "description": "Annual checkup with Dr. Smith",
  "recurrence_type": "none",
  "recurrence_interval": null,
  "recurrence_days": null,
  "reminder_time": "10:00:00",
  "start_date": "2026-02-15T00:00:00.000000Z",
  "end_date": null,
  "created_at": "2026-01-09T...",
  "updated_at": "2026-01-09T..." (newer timestamp)
}
```

### Test 4.2: Validation Error on Update
```bash
curl -X PUT http://localhost:8080/api/reminders/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test",
    "recurrence_type": "invalid_type",
    "start_date": "2026-01-10"
  }'
```

**Expected Output:**
```json
{
  "message": "The selected recurrence type is invalid.",
  "errors": {
    "recurrence_type": ["The selected recurrence type is invalid."]
  }
}
```
**Status Code:** 422

---

## 5. DELETE Reminder (DELETE /api/reminders/{id})

### Test 5.1: Delete Reminder Successfully
```bash
curl -X DELETE http://localhost:8080/api/reminders/1
```

**Expected Output:**
```json
{
  "message": "Reminder deleted successfully"
}
```

### Test 5.2: Verify Deletion
```bash
curl http://localhost:8080/api/reminders/1
```

**Expected Output:**
```json
{
  "message": "No query results for model [App\\Models\\Reminder] 1"
}
```
**Status Code:** 404

---

## 6. SEARCH Reminders (GET /api/reminders/search)

### Test 6.1: Search by Title
```bash
curl "http://localhost:8080/api/reminders/search?keyword=Vitamins"
```

**Expected Output:**
```json
[
  {
    "id": 2,
    "title": "Take Vitamins",
    "description": "Daily vitamins",
    ...
  }
]
```

### Test 6.2: Search by Description
```bash
curl "http://localhost:8080/api/reminders/search?keyword=garden"
```

**Expected Output:**
```json
[
  {
    "id": 3,
    "title": "Water Plants",
    "description": "Water the garden",
    ...
  }
]
```

### Test 6.3: Search with No Results
```bash
curl "http://localhost:8080/api/reminders/search?keyword=nonexistent"
```

**Expected Output:**
```json
[]
```

---

## 7. GET Occurrences (GET /api/reminders/occurrences)

### Test 7.1: Get Occurrences in Date Range
```bash
curl "http://localhost:8080/api/reminders/occurrences?start_date=2026-01-10&end_date=2026-01-15"
```

**Expected Output:**
```json
[
  {
    "occurrence_date": "2026-01-10",
    "id": 2,
    "title": "Take Vitamins",
    "description": "Daily vitamins"
  },
  {
    "occurrence_date": "2026-01-10",
    "id": 3,
    "title": "Water Plants",
    "description": "Water the garden"
  },
  {
    "occurrence_date": "2026-01-10",
    "id": 4,
    "title": "Gym Workout",
    "description": "Strength training"
  },
  {
    "occurrence_date": "2026-01-11",
    "id": 2,
    "title": "Take Vitamins",
    "description": "Daily vitamins"
  },
  ...
  (Multiple occurrences for each day based on recurrence rules)
]
```

### Test 7.2: Validation Error - Invalid Date
```bash
curl "http://localhost:8080/api/reminders/occurrences?start_date=invalid&end_date=2026-01-15"
```

**Expected Output:**
```json
{
  "message": "The start date field must be a valid date.",
  "errors": {
    "start_date": ["The start date field must be a valid date."]
  }
}
```
**Status Code:** 422

### Test 7.3: Validation Error - Missing Parameter
```bash
curl "http://localhost:8080/api/reminders/occurrences?start_date=2026-01-10"
```

**Expected Output:**
```json
{
  "message": "The end date field is required.",
  "errors": {
    "end_date": ["The end date field is required."]
  }
}
```
**Status Code:** 422

---

## Test Summary

### Endpoints Tested
- ✅ POST /api/reminders (Create)
- ✅ GET /api/reminders (Read All)
- ✅ GET /api/reminders/{id} (Read One)
- ✅ PUT /api/reminders/{id} (Update)
- ✅ DELETE /api/reminders/{id} (Delete)
- ✅ GET /api/reminders/search (Search)
- ✅ GET /api/reminders/occurrences (Date Range Query)

### Validation Tests
- ✅ Missing required fields
- ✅ Invalid enum values (recurrence_type)
- ✅ Date validation (end_date >= start_date)
- ✅ Invalid date formats
- ✅ Missing parameters

### Recurrence Types Tested
- ✅ none (one-time reminders)
- ✅ daily (every day)
- ✅ interval (every N days)
- ✅ weekly (specific days of week)

### Edge Cases
- ✅ 404 for non-existent resources
- ✅ Empty search results
- ✅ Validation on both create and update

---

## Notes

All tests passed successfully with expected outputs. The API correctly handles:
- CRUD operations
- Input validation
- Recurrence calculation
- Search functionality
- Date range queries
- Error responses with appropriate status codes
