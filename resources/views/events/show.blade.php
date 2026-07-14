<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Events', 'url' => route('events.index')], ['label' => $event->title]]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($event->banner_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($event->banner_path) }}" class="h-56 w-full object-cover" alt="{{ $event->title }}">
            @else
                <div class="h-40 w-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-4xl font-semibold">
                    {{ mb_substr($event->title, 0, 1) }}
                </div>
            @endif

            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        @php
                            $statusTone = match ($event->status) {
                                \App\Enums\EventStatus::Published => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                \App\Enums\EventStatus::Archived => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                                default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                            };
                        @endphp
                        <span class="rounded-full text-xs px-2.5 py-1 {{ $statusTone }}">{{ $event->status->label() }}</span>
                        <h1 class="text-xl font-semibold mt-2">{{ $event->title }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event->venue }}</p>
                    </div>

                    @can('update', $event)
                        <div class="flex flex-wrap gap-3 shrink-0 items-center">
                            <a href="{{ route('events.edit', $event) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Edit</a>
                            <a href="{{ route('events.participants', $event) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Participants</a>

                            @if ($event->status === \App\Enums\EventStatus::Draft)
                                <form method="POST" action="{{ route('events.publish', $event) }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Publish</button>
                                </form>
                            @elseif ($event->status === \App\Enums\EventStatus::Published)
                                <form method="POST" action="{{ route('events.archive', $event) }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">Archive</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Delete this event? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                            </form>
                        </div>
                    @endcan
                </div>

                <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Date</dt><dd class="mt-0.5">{{ $event->event_date->format('l, F j, Y') }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Time</dt><dd class="mt-0.5">{{ \Illuminate\Support\Carbon::parse($event->event_time)->format('g:i A') }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Registration Deadline</dt><dd class="mt-0.5">{{ $event->registration_deadline->format('M j, Y g:i A') }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Capacity</dt><dd class="mt-0.5">{{ $event->registrations_count }}{{ $event->capacity ? ' / '.$event->capacity : ' registered' }}</dd></div>
                </dl>

                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $event->description }}</p>

                @error('registration')
                    <p class="mt-4 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror

                <div class="mt-6">
                    @if ($isRegistered)
                        <form method="POST" action="{{ route('events.cancel', $event) }}">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">Cancel Registration</x-danger-button>
                        </form>
                    @elseif ($event->isRegistrationOpen())
                        <form method="POST" action="{{ route('events.register', $event) }}">
                            @csrf
                            <x-primary-button type="submit">Register</x-primary-button>
                        </form>
                    @elseif ($event->status === \App\Enums\EventStatus::Published)
                        <p class="text-sm text-gray-400">{{ $event->isFull() ? 'This event is full.' : 'Registration is closed.' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
