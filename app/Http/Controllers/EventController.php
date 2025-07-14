<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->toISOString(),
                'end' => $event->end ? $event->end->toISOString() : null,
                'color' => $event->color,
                'description' => $event->description,
            ];
        });

        return Inertia::render('Calendar', [
            'events' => $events
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $event = Event::create($request->all());

        // Redirect back dengan flash message
        return redirect()->back()->with('success', 'Event berhasil ditambahkan!');
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $event->update($request->all());

        return redirect()->back()->with('success', 'Event berhasil diupdate!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->back()->with('success', 'Event berhasil dihapus!');
    }
}