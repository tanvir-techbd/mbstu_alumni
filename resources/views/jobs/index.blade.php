<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Jobs']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ ($filters['bookmarked'] ?? false) ? 'Bookmarked Jobs' : 'Job Board' }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $jobs->total() }} jobs</p>
            </div>

            @can('create', \App\Models\JobPosting::class)
                <a href="{{ route('jobs.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    Post a Job
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('jobs.index') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Position or company…"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Category</label>
                <input type="text" name="category" value="{{ $filters['category'] ?? '' }}" class="w-32 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Type</label>
                <select name="employment_type" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All types</option>
                    @foreach (\App\Enums\EmploymentType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(($filters['employment_type'] ?? '') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Location</label>
                <input type="text" name="location" value="{{ $filters['location'] ?? '' }}" class="w-32 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">Filter</button>
                <a href="{{ route('jobs.index', ['bookmarked' => ($filters['bookmarked'] ?? false) ? null : 1]) }}"
                   class="rounded-lg border text-sm font-medium px-4 py-2 {{ ($filters['bookmarked'] ?? false) ? 'bg-primary-600 text-white border-primary-600' : 'border-gray-300 dark:border-gray-700' }}">
                    Bookmarked
                </a>
            </div>
        </form>

        @if ($jobs->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state title="No jobs found" description="Try a different search or filter." />
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($jobs as $job)
                    <a href="{{ route('jobs.show', $job) }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5 hover:ring-primary-500 transition">
                        <div class="flex items-center gap-3">
                            @if ($job->company_logo_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($job->company_logo_path) }}" class="h-10 w-10 rounded-lg object-cover" alt="{{ $job->company }}">
                            @else
                                <div class="h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 font-semibold">
                                    {{ mb_substr($job->company, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-medium truncate">{{ $job->position }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $job->company }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-1.5">
                            <span class="text-[11px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $job->employment_type->label() }}</span>
                            <span class="text-[11px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $job->location }}</span>
                            @if ($job->status !== \App\Enums\JobStatus::Published)
                                <span class="text-[11px] bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 rounded-full px-2 py-0.5">{{ $job->status->label() }}</span>
                            @endif
                        </div>

                        <p class="mt-3 text-xs text-gray-400">Deadline: {{ $job->deadline->format('M j, Y') }}</p>
                    </a>
                @endforeach
            </div>

            <div>{{ $jobs->links() }}</div>
        @endif
    </div>
</x-app-layout>
