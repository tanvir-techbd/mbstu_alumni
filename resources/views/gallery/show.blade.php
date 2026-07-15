<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Gallery', 'url' => route('gallery.index')], ['label' => $gallery->title]]" />
    </x-slot>

    <div class="space-y-6" x-data="{ lightbox: null }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <span class="text-[11px] uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $gallery->category->label() }}</span>
                <h1 class="text-xl font-semibold mt-2">{{ $gallery->title }}</h1>
                @if ($gallery->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $gallery->description }}</p>
                @endif
            </div>

            @can('update', $gallery)
                <div class="flex gap-3 shrink-0">
                    <a href="{{ route('gallery.edit', $gallery) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('gallery.destroy', $gallery) }}" onsubmit="return confirm('Delete this album and all its photos? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                    </form>
                </div>
            @endcan
        </div>

        @if ($gallery->images->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state title="No photos in this album yet" />
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($gallery->images as $image)
                    <button type="button" @click="lightbox = '{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}'" class="block rounded-xl overflow-hidden">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}" class="h-36 w-full object-cover hover:opacity-90 transition" loading="lazy" alt="{{ $image->caption ?? $gallery->title }}">
                    </button>
                @endforeach
            </div>
        @endif

        <div
            x-show="lightbox"
            x-cloak
            @click="lightbox = null"
            @keydown.escape.window="lightbox = null"
            class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-6"
        >
            <img :src="lightbox" class="max-h-full max-w-full rounded-lg" alt="">
            <button type="button" @click="lightbox = null" class="absolute top-6 right-6 text-white text-2xl leading-none">&times;</button>
        </div>
    </div>
</x-app-layout>
