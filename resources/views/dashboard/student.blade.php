<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Student'], ['label' => 'Dashboard']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Jobs, mentors, and events picked for you.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Saved Jobs" :value="$savedJobs" />
            <x-stat-card label="Applied Mentorship" :value="$mentorshipRequests" />
            <x-stat-card label="Upcoming Events" :value="$upcomingEvents" />
            <x-stat-card label="Notices" :value="$totalNotices" />
        </div>
    </div>
</x-app-layout>
