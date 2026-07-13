<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Faculty'], ['label' => 'Dashboard']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Events, notices, and alumni engagement at a glance.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <x-stat-card-placeholder label="Published Events" milestone="M5 — Events" />
            <x-stat-card-placeholder label="Notices" milestone="M8 — Notice Board" />
            <x-stat-card-placeholder label="Alumni Statistics" milestone="M4 — Alumni Directory" />
        </div>
    </div>
</x-app-layout>
