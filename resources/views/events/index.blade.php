<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Events']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Events</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $events->total() }} events</p>
            </div>

            @can('create', \App\Models\Event::class)
                <a href="{{ route('events.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    Create Event
                </a>
            @endcan
        </div>

        @if ($events->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state title="No events yet" description="Check back later." />
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($events as $event)
                    <a href="{{ route('events.show', $event) }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden hover:ring-primary-500 transition">
                        @if ($event->banner_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($event->banner_path) }}" class="h-32 w-full object-cover" alt="{{ $event->title }}">
                        @else
                            <div class="h-32 w-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-2xl font-semibold">
                                {{ mb_substr($event->title, 0, 1) }}
                            </div>
                        @endif

                        <div class="p-5">
                            <div class="flex items-center justify-between mb-2">
                                @php
                                    $statusTone = match ($event->status) {
                                        \App\Enums\EventStatus::Published => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                        \App\Enums\EventStatus::Archived => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                    };
                                @endphp
                                <span class="rounded-full text-xs px-2.5 py-1 {{ $statusTone }}">{{ $event->status->label() }}</span>
                                <span class="text-xs text-gray-400">{{ $event->event_date->format('M j, Y') }}</span>
                            </div>

                            <p class="font-medium truncate">{{ $event->title }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $event->venue }}</p>

                            <p class="mt-2 text-xs text-gray-400">
                                {{ $event->registrations_count }} registered{{ $event->capacity ? ' / '.$event->capacity : '' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div>{{ $events->links() }}</div>
        @endif
    </div>
</x-app-layout>
