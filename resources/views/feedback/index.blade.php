<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Feedback']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ auth()->user()->hasRole('super-admin') ? 'Feedback Tickets' : 'My Feedback' }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $tickets->total() }} tickets</p>
            </div>

            <div class="flex gap-2">
                @can('export', \App\Models\FeedbackTicket::class)
                    <a href="{{ route('feedback.export') }}" class="inline-flex items-center rounded-lg bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 text-sm font-semibold px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        Export
                    </a>
                @endcan
                <a href="{{ route('feedback.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    Submit Feedback
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('feedback.index') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Subject…"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Type</label>
                <select name="type" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All statuses</option>
                    <option value="open" @selected(($filters['status'] ?? '') === 'open')>Open</option>
                    <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>Closed</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">Filter</button>
        </form>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($tickets->isEmpty())
                <x-empty-state title="No feedback tickets found" description="Try a different search or filter." />
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($tickets as $ticket)
                        <a href="{{ route('feedback.show', $ticket) }}" class="block p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $ticket->type->label() }}</span>
                                        <span class="text-[11px] uppercase tracking-wide rounded-full px-2 py-0.5 {{ $ticket->isOpen() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">{{ $ticket->status->label() }}</span>
                                    </div>
                                    <p class="font-medium mt-1 truncate">{{ $ticket->subject }}</p>
                                    @if (auth()->user()->hasRole('super-admin'))
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $ticket->user?->name ?? 'Deleted user' }}</p>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 shrink-0">{{ $ticket->created_at->format('M j, Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
