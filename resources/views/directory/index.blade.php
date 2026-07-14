<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Alumni Directory']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Alumni Directory</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $profiles->total() }} verified alumni</p>
        </div>

        <form method="GET" action="{{ route('directory.index') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-4 space-y-3">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name or student ID…"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Department</label>
                    <input type="text" name="department" value="{{ $filters['department'] ?? '' }}" class="w-32 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Batch</label>
                    <input type="text" name="batch" value="{{ $filters['batch'] ?? '' }}" class="w-24 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Grad. Year</label>
                    <input type="number" name="graduation_year" value="{{ $filters['graduation_year'] ?? '' }}" class="w-28 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Company</label>
                    <input type="text" name="company" value="{{ $filters['company'] ?? '' }}" class="w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Country</label>
                    <input type="text" name="country" value="{{ $filters['country'] ?? '' }}" class="w-32 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">District</label>
                    <input type="text" name="district" value="{{ $filters['district'] ?? '' }}" class="w-32 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Skills</label>
                    <input type="text" name="skills" value="{{ $filters['skills'] ?? '' }}" placeholder="e.g. Laravel" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sort</label>
                    <select name="sort" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest Joined</option>
                        <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name</option>
                        <option value="graduation_year" @selected(($filters['sort'] ?? '') === 'graduation_year')>Graduation Year</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">Search</button>
                @if (collect($filters)->filter()->isNotEmpty())
                    <a href="{{ route('directory.index') }}" class="rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium px-4 py-2">Clear</a>
                @endif
            </div>
        </form>

        @if ($profiles->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state title="No alumni found" description="Try a different search or filter." />
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($profiles as $profile)
                    <a href="{{ route('directory.show', $profile) }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5 hover:ring-primary-500 transition">
                        <div class="flex items-center gap-3">
                            @if ($profile->user->profile_photo_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->user->profile_photo_path) }}" class="h-12 w-12 rounded-full object-cover" alt="{{ $profile->user->name }}">
                            @else
                                <div class="h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 font-semibold">
                                    {{ mb_substr($profile->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-medium truncate">{{ $profile->user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $profile->designation ?? 'Alumni' }}{{ $profile->company ? ' at '.$profile->company : '' }}</p>
                            </div>
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <div><dt class="inline font-medium">Dept:</dt> {{ $profile->department ?? '—' }}</div>
                            <div><dt class="inline font-medium">Batch:</dt> {{ $profile->batch ?? '—' }}</div>
                            <div><dt class="inline font-medium">Class of:</dt> {{ $profile->graduation_year ?? '—' }}</div>
                            <div><dt class="inline font-medium">Location:</dt> {{ $profile->district ?? $profile->country ?? '—' }}</div>
                        </dl>

                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                            <p class="truncate">{{ $profile->user->email }}</p>
                            @if ($profile->user->phone)
                                <p>{{ $profile->user->phone }}</p>
                            @endif
                        </div>

                        @if ($profile->skillList())
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach (array_slice($profile->skillList(), 0, 3) as $skill)
                                    <span class="text-[11px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <div>{{ $profiles->links() }}</div>
        @endif
    </div>
</x-app-layout>
