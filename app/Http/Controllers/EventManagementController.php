<?php

namespace App\Http\Controllers;

use App\Exports\EventParticipantsExport;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EventManagementController extends Controller
{
    public function __construct(private readonly EventService $events)
    {
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = $this->events->create($request->validated(), $request->user(), $request->file('banner'));

        return redirect()->route('events.show', $event)->with('success', 'Event created as a draft. Publish it when ready.');
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('events.edit', ['event' => $event]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $this->events->update($event, $request->validated(), $request->file('banner'));

        return redirect()->route('events.show', $event)->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $this->events->delete($event);

        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);

        $this->events->publish($event);

        return back()->with('success', 'Event published.');
    }

    public function archive(Event $event): RedirectResponse
    {
        $this->authorize('archive', $event);

        $this->events->archive($event);

        return back()->with('success', 'Event archived.');
    }

    public function participants(Event $event): View
    {
        $this->authorize('manageParticipants', $event);

        return view('events.participants', [
            'event' => $event,
            'registrations' => $event->registrations()->with('user')->latest()->get(),
        ]);
    }

    public function exportParticipants(Event $event): BinaryFileResponse
    {
        $this->authorize('manageParticipants', $event);

        return Excel::download(new EventParticipantsExport($event), 'event-'.$event->id.'-participants.xlsx');
    }

    public function markAttendance(Request $request, Event $event, User $user): RedirectResponse
    {
        $this->authorize('manageParticipants', $event);

        $this->events->markAttendance($event, $user, $request->boolean('attended'));

        return back()->with('success', 'Attendance updated.');
    }
}
