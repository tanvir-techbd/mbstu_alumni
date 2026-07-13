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
            <x-stat-card-placeholder label="Profile Completion" milestone="M3 — Alumni Profile + Verification" />
            <x-stat-card-placeholder label="Upcoming Events" milestone="M5 — Events" />
            <x-stat-card-placeholder label="Posted Jobs" milestone="M6 — Job Portal" />
            <x-stat-card-placeholder label="Mentorship Requests" milestone="M7 — Mentorship" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-stat-card-placeholder label="Donation History" milestone="M10 — Donations" />
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5 flex flex-col justify-center items-center text-center gap-2">
                <p class="text-sm font-medium">Verification status</p>
                <span class="rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 text-xs px-2.5 py-1">Not started — coming in M3</span>
            </div>
        </div>
    </div>
</x-app-layout>
