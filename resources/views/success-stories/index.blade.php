<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Success Stories']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Success Stories</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stories->total() }} stories</p>
            </div>

            @can('create', \App\Models\SuccessStory::class)
                <a href="{{ route('success-stories.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    Submit Your Story
                </a>
            @endcan
        </div>

        @if ($stories->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state title="No stories yet" description="Be the first to share your journey." />
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($stories as $story)
                    <a href="{{ route('success-stories.show', $story) }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden hover:ring-primary-500 transition">
                        @if ($story->images->isNotEmpty())
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($story->images->first()->image_path) }}" class="h-36 w-full object-cover" alt="{{ $story->title }}">
                        @else
                            <div class="h-36 w-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-2xl font-semibold">
                                {{ mb_substr($story->title, 0, 1) }}
                            </div>
                        @endif

                        <div class="p-5">
                            @if ($story->status !== \App\Enums\SuccessStoryStatus::Published)
                                @php
                                    $statusTone = $story->status === \App\Enums\SuccessStoryStatus::Rejected
                                        ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
                                @endphp
                                <span class="rounded-full text-xs px-2.5 py-1 {{ $statusTone }} mb-2 inline-block">{{ $story->status->label() }}</span>
                            @endif
                            <p class="font-medium truncate">{{ $story->title }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $story->user?->name }}{{ $story->company ? ' · '.$story->company : '' }}</p>
                            @if ($story->achievement)
                                <p class="mt-2 text-xs text-primary-600 dark:text-primary-400">{{ $story->achievement }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div>{{ $stories->links() }}</div>
        @endif
    </div>
</x-app-layout>
