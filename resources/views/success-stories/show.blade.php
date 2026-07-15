<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Success Stories', 'url' => route('success-stories.index')], ['label' => $story->title]]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        @if ($story->images->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach ($story->images as $image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}" class="h-32 w-full object-cover rounded-xl" alt="{{ $story->title }}">
                @endforeach
            </div>
        @endif

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    @if ($story->status !== \App\Enums\SuccessStoryStatus::Published)
                        @php
                            $statusTone = $story->status === \App\Enums\SuccessStoryStatus::Rejected
                                ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'
                                : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
                        @endphp
                        <span class="rounded-full text-xs px-2.5 py-1 {{ $statusTone }}">{{ $story->status->label() }}</span>
                    @endif
                    <h1 class="text-xl font-semibold mt-2">{{ $story->title }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $story->user?->name }}{{ $story->company ? ' · '.$story->company : '' }}
                    </p>
                    @if ($story->achievement)
                        <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">{{ $story->achievement }}</p>
                    @endif
                </div>
            </div>

            @if ($story->status === \App\Enums\SuccessStoryStatus::Rejected && $story->rejection_reason)
                <div class="mt-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 ring-1 ring-rose-200 dark:ring-rose-500/20 p-4 text-sm text-rose-700 dark:text-rose-400">
                    <p class="font-medium">This story was not approved</p>
                    <p class="mt-1">{{ $story->rejection_reason }}</p>
                </div>
            @endif

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $story->story }}</p>

            @can('update', $story)
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-center">
                    <a href="{{ route('success-stories.edit', $story) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Edit</a>

                    <form method="POST" action="{{ route('success-stories.destroy', $story) }}" onsubmit="return confirm('Delete this story? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                    </form>

                    @can('review', \App\Models\SuccessStory::class)
                        @if ($story->status === \App\Enums\SuccessStoryStatus::Pending)
                            <div x-data="{ rejecting: false }" class="flex items-center gap-3">
                                <form method="POST" action="{{ route('success-stories.approve', $story) }}" x-show="!rejecting">
                                    @csrf
                                    <button type="submit" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Approve</button>
                                </form>
                                <button type="button" @click="rejecting = true" x-show="!rejecting" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Reject</button>

                                <form method="POST" action="{{ route('success-stories.reject', $story) }}" x-show="rejecting" x-cloak class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="rejection_reason" placeholder="Reason" required
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
