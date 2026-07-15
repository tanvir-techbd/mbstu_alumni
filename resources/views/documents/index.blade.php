<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Documents']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Documents</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $documents->total() }} documents</p>
            </div>

            @can('create', \App\Models\Document::class)
                <a href="{{ route('documents.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    Upload Document
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('documents.index') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Title…"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Category</label>
                <select name="category" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->value }}" @selected(($filters['category'] ?? '') === $category->value)>{{ $category->label() }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">Filter</button>
        </form>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($documents->isEmpty())
                <x-empty-state title="No documents found" description="Try a different search or filter." />
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($documents as $document)
                        <div class="flex items-start justify-between gap-4 p-5">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $document->category->label() }}</span>
                                    <span class="text-xs text-gray-400">{{ $document->formattedSize() }}</span>
                                </div>
                                <p class="font-medium mt-1 truncate">{{ $document->title }}</p>
                                @if ($document->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $document->description }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    Uploaded {{ $document->created_at->format('M j, Y') }}
                                    @if ($document->uploader) by {{ $document->uploader->name }} @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ route('documents.download', $document) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Download</a>

                                @can('update', $document)
                                    <a href="{{ route('documents.edit', $document) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">Edit</a>
                                @endcan

                                @can('delete', $document)
                                    <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Delete this document? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
