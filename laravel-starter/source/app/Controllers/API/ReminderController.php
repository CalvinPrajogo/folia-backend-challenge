<?php

namespace App\Controllers\API;


use App\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

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
        $reminder = Reminder::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'recurrence_type' => $request->input('recurrence_type'),
            'recurrence_interval' => $request->input('recurrence_interval'),
            'recurrence_days' => $request->input('recurrence_days'),
            'reminder_time' => $request->input('reminder_time'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);

        return $reminder;
    }

    //Update a reminder
    public function update(Request $request, string $id)
    {
        $reminder = Reminder::findOrFail($id);
        
        $reminder->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'recurrence_type' => $request->input('recurrence_type'),
            'recurrence_interval' => $request->input('recurrence_interval'),
            'recurrence_days' => $request->input('recurrence_days'),
            'reminder_time' => $request->input('reminder_time'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);

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
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $allOccurrences = [];
        
        foreach (Reminder::all() as $reminder) {
            $occurrences = $reminder->getOccurrences(
                new \DateTime($startDate),
                new \DateTime($endDate)
            );
            $allOccurrences = array_merge($allOccurrences, $occurrences);
        }
        
        return $allOccurrences;
    }
}
