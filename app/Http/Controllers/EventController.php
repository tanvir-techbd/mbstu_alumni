<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function __construct(private readonly EventService $events)
    {
    }

    public function index(Request $request): View
    {
        $events = Event::query()
            ->visibleTo($request->user())
            ->withCount('registrations')
            ->orderBy('event_date')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', ['events' => $events]);
    }

    public function show(Request $request, Event $event): View
    {
        $this->authorize('view', $event);

        return view('events.show', [
            'event' => $event->loadCount('registrations'),
            'isRegistered' => $event->isRegisteredBy($request->user()),
        ]);
    }

    public function register(Request $request, Event $event): RedirectResponse
    {
        try {
            $this->events->register($event, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'You are registered for this event.');
    }

    public function cancelRegistration(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('cancelRegistration', $event);

        $this->events->cancelRegistration($event, $request->user());

        return back()->with('success', 'Registration cancelled.');
    }
}
