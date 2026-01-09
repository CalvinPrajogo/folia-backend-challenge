<?php

namespace App\Controllers\API;


use App\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReminderController extends Controller
{
    // Get all reminders
    public function index()
    {
        return Reminder::all();
    }

    // Get one reminder
    public function show(string $id)
    {
        return Reminder::findOrFail($id);
    }

    // Create a reminder
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recurrence_type' => 'required|in:none,daily,interval,weekly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_days' => 'nullable|array',
            'recurrence_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'reminder_time' => 'nullable|date_format:H:i:s',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $reminder = Reminder::create($validated);

        return $reminder;
    }

    //Update a reminder
    public function update(Request $request, string $id)
    {
        $reminder = Reminder::findOrFail($id);
        
        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recurrence_type' => 'required|in:none,daily,interval,weekly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_days' => 'nullable|array',
            'recurrence_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'reminder_time' => 'nullable|date_format:H:i:s',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $reminder->update($validated);

        return $reminder;
    }

    // Delete a reminder
    public function destroy(string $id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted successfully']);
    }

    // Search for reminder based off keyword
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        
        return Reminder::where('title', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->get();
    }

    // Get occurrences of reminders in a date range
    public function occurrences(Request $request) {
        // Validate input
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        
        $allOccurrences = [];
        
        foreach (Reminder::all() as $reminder) {
            $occurrences = $reminder->getOccurrences(
                Carbon::parse($startDate),
                Carbon::parse($endDate)
            );
            $allOccurrences = array_merge($allOccurrences, $occurrences);
        }
        
        return $allOccurrences;
    }
}
