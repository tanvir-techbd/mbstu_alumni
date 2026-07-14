<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Jobs', 'url' => route('jobs.index')], ['label' => $job->position]]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if ($job->company_logo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($job->company_logo_path) }}" class="h-14 w-14 rounded-lg object-cover" alt="{{ $job->company }}">
                    @else
                        <div class="h-14 w-14 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 text-xl font-semibold">
                            {{ mb_substr($job->company, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-semibold">{{ $job->position }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $job->company }} · {{ $job->location }}</p>
                    </div>
                </div>

                @if ($job->status !== \App\Enums\JobStatus::Published)
                    @php
                        $statusTone = $job->status === \App\Enums\JobStatus::Rejected
                            ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'
                            : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
                    @endphp
                    <span class="rounded-full text-xs px-2.5 py-1 shrink-0 {{ $statusTone }}">{{ $job->status->label() }}</span>
                @endif
            </div>

            @if ($job->status === \App\Enums\JobStatus::Rejected && $job->rejection_reason)
                <div class="mt-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 ring-1 ring-rose-200 dark:ring-rose-500/20 p-4 text-sm text-rose-700 dark:text-rose-400">
                    <p class="font-medium">This posting was rejected</p>
                    <p class="mt-1">{{ $job->rejection_reason }}</p>
                </div>
            @endif

            <div class="mt-4 flex flex-wrap gap-1.5">
                <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2.5 py-1">{{ $job->employment_type->label() }}</span>
                <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2.5 py-1">{{ $job->category }}</span>
                @if ($job->salary)<span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2.5 py-1">{{ $job->salary }}</span>@endif
                @if ($job->experience)<span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2.5 py-1">{{ $job->experience }} experience</span>@endif
            </div>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $job->description }}</p>

            <p class="mt-4 text-xs text-gray-400">Application deadline: {{ $job->deadline->format('F j, Y') }}{{ $job->isExpired() ? ' (expired)' : '' }}</p>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                @if ($job->status === \App\Enums\JobStatus::Published && ! $job->isExpired())
                    <a href="{{ $job->apply_url }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                        Apply Now
                    </a>
                @endif

                <form method="POST" action="{{ route('jobs.bookmark', $job) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium px-4 py-2">
                        <svg class="h-4 w-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                        {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                    </button>
                </form>
            </div>

            @can('update', $job)
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-center">
                    <a href="{{ route('jobs.edit', $job) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Edit</a>

                    <form method="POST" action="{{ route('jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job posting? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                    </form>

                    @can('review', \App\Models\JobPosting::class)
                        @if ($job->status === \App\Enums\JobStatus::Pending)
                            <div x-data="{ rejecting: false }" class="flex items-center gap-3">
                                <form method="POST" action="{{ route('jobs.approve', $job) }}" x-show="!rejecting">
                                    @csrf
                                    <button type="submit" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Approve</button>
                                </form>
                                <button type="button" @click="rejecting = true" x-show="!rejecting" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Reject</button>

                                <form method="POST" action="{{ route('jobs.reject', $job) }}" x-show="rejecting" x-cloak class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="rejection_reason" placeholder="Reason for rejection" required
                                           class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                                    <x-danger-button type="submit">Confirm</x-danger-button>
                                    <x-secondary-button type="button" @click="rejecting = false">Cancel</x-secondary-button>
                                </form>
                            </div>
                        @endif
                    @endcan
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
