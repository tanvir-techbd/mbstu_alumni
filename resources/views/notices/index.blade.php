<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Notice Board']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ ($filters['bookmarked'] ?? false) ? 'Bookmarked Notices' : 'Notice Board' }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $notices->total() }} notices</p>
            </div>

            @can('create', \App\Models\Notice::class)
                <a href="{{ route('notices.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    Publish Notice
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('notices.index') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Title or content…"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Type</label>
                <select name="type" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All types</option>
                    @foreach (\App\Enums\NoticeType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">Filter</button>
                <a href="{{ route('notices.index', ['bookmarked' => ($filters['bookmarked'] ?? false) ? null : 1]) }}"
                   class="rounded-lg border text-sm font-medium px-4 py-2 {{ ($filters['bookmarked'] ?? false) ? 'bg-primary-600 text-white border-primary-600' : 'border-gray-300 dark:border-gray-700' }}">
                    Bookmarked
                </a>
            </div>
        </form>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($notices->isEmpty())
                <x-empty-state title="No notices found" description="Try a different search or filter." />
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($notices as $notice)
                        <a href="{{ route('notices.show', $notice) }}" class="block p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $notice->type->label() }}</span>
                                        @if ($notice->attachment_path)
                                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                        @endif
                                    </div>
                                    <p class="font-medium mt-1 truncate">{{ $notice->title }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $notice->content }}</p>
                                </div>
                                <p class="text-xs text-gray-400 shrink-0">{{ $notice->created_at->format('M j, Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $notices->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
