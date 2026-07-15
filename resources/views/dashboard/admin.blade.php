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
            <x-stat-card label="Verified Alumni" :value="$verifiedAlumni" />
            <x-stat-card label="Students" :value="$totalStudents" />
            <x-stat-card label="Faculty" :value="$totalFaculty" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Events" :value="$totalEvents" />
            <x-stat-card label="Jobs" :value="$totalJobs" />
            <x-stat-card label="Donations" value="{{ '৳'.number_format((float) $totalDonations, 2) }}" />
            <x-stat-card label="Pending Verification" :value="$pendingVerification" :hint="$pendingVerification > 0 ? 'Needs review' : null" hintTone="warning" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5">
                <p class="text-sm font-medium mb-4">Alumni by Department</p>
                @if (empty($alumniByDepartment['labels']))
                    <div class="h-56 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-xs text-gray-400 text-center px-6">
                        No verified alumni with a department on file yet.
                    </div>
                @else
                    <div class="h-56"><canvas id="alumniByDepartmentChart"></canvas></div>
                @endif
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5">
                <p class="text-sm font-medium mb-4">Monthly Donations</p>
                <div class="h-56"><canvas id="monthlyDonationsChart"></canvas></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
                const textColor = isDark ? '#9ca3af' : '#6b7280';

                @if (! empty($alumniByDepartment['labels']))
                    new Chart(document.getElementById('alumniByDepartmentChart'), {
                        type: 'bar',
                        data: {
                            labels: @json($alumniByDepartment['labels']),
                            datasets: [{
                                data: @json($alumniByDepartment['totals']),
                                backgroundColor: '#4f46e5',
                                borderRadius: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: textColor } },
                                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } },
                            },
                        },
                    });
                @endif

                new Chart(document.getElementById('monthlyDonationsChart'), {
                    type: 'line',
                    data: {
                        labels: @json($monthlyDonations['labels']),
                        datasets: [{
                            data: @json($monthlyDonations['totals']),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79,70,229,0.1)',
                            fill: true,
                            tension: 0.3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: textColor } },
                            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
                        },
                    },
                });
            });
        </script>
    @endpush
</x-app-layout>
