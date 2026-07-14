<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Dashboard']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Here's what's happening across the alumni network today.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Alumni" :value="$totalAlumni" />
            <x-stat-card label="Students" :value="$totalStudents" />
            <x-stat-card label="Faculty" :value="$totalFaculty" />
            <x-stat-card label="Total Users" :value="$totalUsers" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Verified Alumni" :value="$verifiedAlumni" />
            <x-stat-card label="Pending Verification" :value="$pendingVerification" :hint="$pendingVerification > 0 ? 'Needs review' : null" hintTone="warning" />
            <x-stat-card label="Events" :value="$totalEvents" />
            <x-stat-card-placeholder label="Jobs" milestone="M6 — Job Portal" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5">
                <p class="text-sm font-medium mb-4">Alumni by Department</p>
                <div class="h-56 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-xs text-gray-400 text-center px-6">
                    Chart.js bar chart — wired once Alumni Profile (M3) adds a department field
                </div>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5">
                <p class="text-sm font-medium mb-4">Monthly Donations</p>
                <div class="h-56 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-xs text-gray-400 text-center px-6">
                    Chart.js line chart — wired once Donations (M10) is built
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
