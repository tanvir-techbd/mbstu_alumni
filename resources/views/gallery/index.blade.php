<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Gallery']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Gallery</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $galleries->total() }} albums</p>
            </div>
            @can('create', \App\Models\Gallery::class)
                <a href="{{ route('gallery.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    New Album
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('gallery.index') }}" class="flex flex-wrap gap-2">
            <a href="{{ route('gallery.index') }}" class="rounded-lg px-3 py-1.5 text-sm {{ empty($filters['category']) ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 text-gray-600 dark:text-gray-300' }}">All</a>
            @foreach (\App\Enums\GalleryCategory::cases() as $category)
                <a href="{{ route('gallery.index', ['category' => $category->value]) }}"
                   class="rounded-lg px-3 py-1.5 text-sm {{ ($filters['category'] ?? '') === $category->value ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 text-gray-600 dark:text-gray-300' }}">
                    {{ $category->label() }}
                </a>
            @endforeach
        </form>

        @if ($galleries->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state title="No albums yet" />
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($galleries as $gallery)
                    <a href="{{ route('gallery.show', $gallery) }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden hover:ring-primary-500 transition">
                        @if ($gallery->coverImage())
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($gallery->coverImage()->image_path) }}" class="h-40 w-full object-cover" loading="lazy" alt="{{ $gallery->title }}">
                        @else
                            <div class="h-40 w-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-2xl font-semibold">
                                {{ mb_substr($gallery->title, 0, 1) }}
                            </div>
                        @endif

                        <div class="p-4">
                            <span class="text-[11px] uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $gallery->category->label() }}</span>
                            <p class="font-medium mt-2 truncate">{{ $gallery->title }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $gallery->images->count() }} photo(s)</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div>{{ $galleries->links() }}</div>
        @endif
    </div>
</x-app-layout>
