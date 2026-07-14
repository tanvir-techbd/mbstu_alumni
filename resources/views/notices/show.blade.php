<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Notice Board', 'url' => route('notices.index')], ['label' => $notice->title]]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="text-[11px] uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $notice->type->label() }}</span>
                    <h1 class="text-xl font-semibold mt-2">{{ $notice->title }}</h1>
                    <p class="text-xs text-gray-400 mt-1">
                        Posted {{ $notice->created_at->format('F j, Y') }}{{ $notice->poster ? ' by '.$notice->poster->name : '' }}
                    </p>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $notice->content }}</p>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                @if ($notice->attachment_path)
                    <a href="{{ route('notices.download', $notice) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                        Download Attachment
                    </a>
                @endif

                <form method="POST" action="{{ route('notices.bookmark', $notice) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium px-4 py-2">
                        <svg class="h-4 w-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                        {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                    </button>
                </form>
            </div>

            @can('update', $notice)
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex gap-3">
                    <a href="{{ route('notices.edit', $notice) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('notices.destroy', $notice) }}" onsubmit="return confirm('Delete this notice? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
