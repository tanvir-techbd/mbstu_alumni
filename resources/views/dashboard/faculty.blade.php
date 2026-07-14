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
            <x-stat-card label="Published Events" :value="$publishedEvents" />
            <x-stat-card label="Notices Posted" :value="$postedNotices" />
            <x-stat-card label="Verified Alumni" :value="$verifiedAlumni" />
        </div>
    </div>
</x-app-layout>
