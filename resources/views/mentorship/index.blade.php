<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Mentorship']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">{{ $viewingAsMentor ? 'Mentorship Requests' : 'My Mentorship Requests' }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $viewingAsMentor ? 'Students who have requested you as a mentor.' : 'Requests you\'ve sent to alumni mentors. Find a mentor from the Alumni Directory.' }}
            </p>
        </div>

        @if ($requests->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state
                    :title="$viewingAsMentor ? 'No requests yet' : 'No mentorship requests yet'"
                    :description="$viewingAsMentor ? null : 'Browse the Alumni Directory and request a mentor.'"
                />
            </div>
        @else
            <div class="space-y-4">
                @foreach ($requests as $request)
                    @php
                        $statusTone = match ($request->status) {
                            \App\Enums\MentorshipStatus::Accepted => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                            \App\Enums\MentorshipStatus::Rejected => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
                            \App\Enums\MentorshipStatus::Completed => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                            default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                        };
                    @endphp
                    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-medium">{{ $viewingAsMentor ? $request->student->name : $request->mentor->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $viewingAsMentor ? $request->student->email : $request->mentor->email }}</p>
                            </div>
                            <span class="rounded-full text-xs px-2.5 py-1 shrink-0 {{ $statusTone }}">{{ $request->status->label() }}</span>
                        </div>

                        @if ($request->message)
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ $request->message }}</p>
                        @endif

                        @if ($request->status === \App\Enums\MentorshipStatus::Rejected && $request->rejection_reason)
                            <p class="mt-3 text-sm text-rose-600 dark:text-rose-400">Reason: {{ $request->rejection_reason }}</p>
                        @endif

                        @if ($request->meeting_scheduled_at)
                            <div class="mt-3 rounded-lg bg-primary-50 dark:bg-primary-500/10 p-3 text-sm text-primary-700 dark:text-primary-400">
                                <p class="font-medium">Meeting scheduled: {{ $request->meeting_scheduled_at->format('F j, Y g:i A') }}</p>
                                @if ($request->meeting_notes)
                                    <p class="mt-1">{{ $request->meeting_notes }}</p>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-3 items-center" x-data="{ rejecting: false, scheduling: false }">
                            @if ($viewingAsMentor && $request->status === \App\Enums\MentorshipStatus::Pending)
                                <form method="POST" action="{{ route('mentorship.accept', $request) }}" x-show="!rejecting">
                                    @csrf
                                    <button type="submit" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Accept</button>
                                </form>
                                <button type="button" @click="rejecting = true" x-show="!rejecting" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Reject</button>

                                <form method="POST" action="{{ route('mentorship.reject', $request) }}" x-show="rejecting" x-cloak class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="rejection_reason" placeholder="Reason" required
                                           class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <x-danger-button type="submit">Confirm</x-danger-button>
                                    <x-secondary-button type="button" @click="rejecting = false">Cancel</x-secondary-button>
                                </form>
                            @endif

                            @if ($viewingAsMentor && $request->status === \App\Enums\MentorshipStatus::Accepted)
                                <button type="button" @click="scheduling = true" x-show="!scheduling" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ $request->meeting_scheduled_at ? 'Reschedule Meeting' : 'Schedule Meeting' }}
                                </button>

                                <form method="POST" action="{{ route('mentorship.schedule', $request) }}" x-show="scheduling" x-cloak class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    <input type="datetime-local" name="meeting_scheduled_at" required
                                           class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <input type="text" name="meeting_notes" placeholder="Location or link (optional)"
                                           class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <x-primary-button type="submit">Save</x-primary-button>
                                    <x-secondary-button type="button" @click="scheduling = false">Cancel</x-secondary-button>
                                </form>

                                <form method="POST" action="{{ route('mentorship.complete', $request) }}" x-show="!scheduling">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">Mark Completed</button>
                                </form>
                            @endif

                            @if (! $viewingAsMentor && $request->status === \App\Enums\MentorshipStatus::Pending)
                                <form method="POST" action="{{ route('mentorship.withdraw', $request) }}" onsubmit="return confirm('Withdraw this mentorship request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Withdraw</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
