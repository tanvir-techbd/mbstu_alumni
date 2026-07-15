<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Alumni'], ['label' => 'Dashboard']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Your profile, network, and activity in one place.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Profile Completion" :value="$profile->completionPercentage() . '%'" />
            <x-stat-card label="Upcoming Events" :value="$upcomingEvents" />
            <x-stat-card label="Posted Jobs" :value="$postedJobs" />
            <x-stat-card label="Mentorship Requests" :value="$pendingMentorshipRequests" :hint="$pendingMentorshipRequests > 0 ? 'Needs response' : null" hintTone="warning" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('donations.history') }}" class="block">
                <x-stat-card label="Donation History" value="{{ '৳'.number_format((float) $totalDonated, 2) }}" hint="{{ $donationCount }} donation(s) — view history" />
            </a>

            @php
                $statusTone = match ($profile->verification_status) {
                    \App\Enums\VerificationStatus::Approved => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                    \App\Enums\VerificationStatus::Rejected => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
                    default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                };
            @endphp
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5 flex flex-col justify-center items-center text-center gap-2">
                <p class="text-sm font-medium">Verification status</p>
                <span class="rounded-full text-xs px-2.5 py-1 {{ $statusTone }}">{{ $profile->verification_status->label() }}</span>
                <a href="{{ route('alumni.profile.edit') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                    {{ $profile->verification_status === \App\Enums\VerificationStatus::Approved ? 'View profile' : 'Complete your profile' }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
