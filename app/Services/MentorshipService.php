<?php

namespace App\Services;

use App\Enums\MentorshipStatus;
use App\Models\MentorshipRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class MentorshipService
{
    public function request(User $student, User $mentor, ?string $message): MentorshipRequest
    {
        $hasActiveRequest = MentorshipRequest::where('student_id', $student->id)
            ->where('mentor_id', $mentor->id)
            ->whereIn('status', [MentorshipStatus::Pending, MentorshipStatus::Accepted])
            ->exists();

        if ($hasActiveRequest) {
            throw ValidationException::withMessages([
                'mentorship' => 'You already have an active mentorship request with this mentor.',
            ]);
        }

        return MentorshipRequest::create([
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'message' => $message,
        ]);
    }

    public function accept(MentorshipRequest $request): void
    {
        $request->forceFill(['status' => MentorshipStatus::Accepted])->save();
    }

    public function reject(MentorshipRequest $request, string $reason): void
    {
        $request->forceFill([
            'status' => MentorshipStatus::Rejected,
            'rejection_reason' => $reason,
        ])->save();
    }

    public function scheduleMeeting(MentorshipRequest $request, string $when, ?string $notes): void
    {
        $request->forceFill([
            'meeting_scheduled_at' => $when,
            'meeting_notes' => $notes,
        ])->save();
    }

    public function complete(MentorshipRequest $request): void
    {
        $request->forceFill([
            'status' => MentorshipStatus::Completed,
            'completed_at' => now(),
        ])->save();
    }

    public function withdraw(MentorshipRequest $request): void
    {
        $request->delete();
    }
}
