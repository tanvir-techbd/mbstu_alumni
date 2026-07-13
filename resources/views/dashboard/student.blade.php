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
            <x-stat-card-placeholder label="Saved Jobs" milestone="M6 — Job Portal" />
            <x-stat-card-placeholder label="Applied Mentorship" milestone="M7 — Mentorship" />
            <x-stat-card-placeholder label="Upcoming Events" milestone="M5 — Events" />
            <x-stat-card-placeholder label="Notices" milestone="M8 — Notice Board" />
        </div>
    </div>
</x-app-layout>
