<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Http\Requests\Mentorship\RejectMentorshipRequestRequest;
use App\Http\Requests\Mentorship\ScheduleMeetingRequest;
use App\Http\Requests\Mentorship\StoreMentorshipRequestRequest;
use App\Models\MentorshipRequest;
use App\Models\User;
use App\Services\MentorshipService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MentorshipController extends Controller
{
    public function __construct(private readonly MentorshipService $mentorship)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $requests = $user->hasRole(RoleName::Alumni->value)
            ? MentorshipRequest::forMentor($user)->with('student')->latest()->get()
            : MentorshipRequest::forStudent($user)->with('mentor')->latest()->get();

        return view('mentorship.index', [
            'requests' => $requests,
            'viewingAsMentor' => $user->hasRole(RoleName::Alumni->value),
        ]);
    }

    public function store(StoreMentorshipRequestRequest $request, User $mentor): RedirectResponse
    {
        try {
            $this->mentorship->request($request->user(), $mentor, $request->validated('message'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Mentorship request sent.');
    }

    public function accept(MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        $this->authorize('respond', $mentorshipRequest);

        $this->mentorship->accept($mentorshipRequest);

        return back()->with('success', 'Mentorship request accepted.');
    }

    public function reject(RejectMentorshipRequestRequest $request, MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        $this->mentorship->reject($mentorshipRequest, $request->validated('rejection_reason'));

        return back()->with('success', 'Mentorship request rejected.');
    }

    public function schedule(ScheduleMeetingRequest $request, MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        $this->mentorship->scheduleMeeting(
            $mentorshipRequest,
            $request->validated('meeting_scheduled_at'),
            $request->validated('meeting_notes')
        );

        return back()->with('success', 'Meeting scheduled.');
    }

    public function complete(MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        $this->authorize('respond', $mentorshipRequest);

        $this->mentorship->complete($mentorshipRequest);

        return back()->with('success', 'Mentorship marked as completed.');
    }

    public function withdraw(MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        $this->authorize('withdraw', $mentorshipRequest);

        $this->mentorship->withdraw($mentorshipRequest);

        return redirect()->route('mentorship.index')->with('success', 'Mentorship request withdrawn.');
    }
}
