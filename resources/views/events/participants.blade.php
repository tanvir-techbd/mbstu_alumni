<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Events', 'url' => route('events.index')], ['label' => $event->title, 'url' => route('events.show', $event)], ['label' => 'Participants']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Participants — {{ $event->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $registrations->count() }} registered</p>
            </div>

            @if ($registrations->isNotEmpty())
                <a href="{{ route('events.participants.export', $event) }}" class="inline-flex items-center rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">
                    Export to Excel
                </a>
            @endif
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($registrations->isEmpty())
                <x-empty-state title="No registrations yet" />
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="text-left font-medium px-5 py-3">Name</th>
                            <th class="text-left font-medium px-5 py-3">Email</th>
                            <th class="text-left font-medium px-5 py-3">Phone</th>
                            <th class="text-left font-medium px-5 py-3">Registered</th>
                            <th class="text-right font-medium px-5 py-3">Attended</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($registrations as $registration)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $registration->user->name }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $registration->user->email }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $registration->user->phone ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $registration->created_at->format('M j, Y g:i A') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="{{ route('events.attendance', [$event, $registration->user]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="attended" value="{{ $registration->attended ? '0' : '1' }}">
                                        <button type="submit" class="rounded-full text-xs px-2.5 py-1 {{ $registration->attended ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                            {{ $registration->attended ? 'Attended' : 'Not marked' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
