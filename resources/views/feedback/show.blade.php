<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Feedback', 'url' => route('feedback.index')], ['label' => $ticket->subject]]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $ticket->type->label() }}</span>
                        <span class="text-[11px] uppercase tracking-wide rounded-full px-2 py-0.5 {{ $ticket->isOpen() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">{{ $ticket->status->label() }}</span>
                    </div>
                    <h1 class="text-xl font-semibold mt-2">{{ $ticket->subject }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Submitted by {{ $ticket->user?->name ?? 'Deleted user' }} on {{ $ticket->created_at->format('M j, Y g:i A') }}
                    </p>
                </div>

                @can('close', $ticket)
                    @if ($ticket->isOpen())
                        <form method="POST" action="{{ route('feedback.close', $ticket) }}" onsubmit="return confirm('Close this ticket? No further replies will be accepted.')">
                            @csrf
                            <button type="submit" class="rounded-lg border border-rose-300 dark:border-rose-800 text-rose-600 dark:text-rose-400 text-sm font-medium px-4 py-2 hover:bg-rose-50 dark:hover:bg-rose-900/20">
                                Close Ticket
                            </button>
                        </form>
                    @endif
                @endcan
            </div>

            <p class="mt-4 text-sm whitespace-pre-line">{{ $ticket->message }}</p>
        </div>

        <div class="space-y-4">
            @forelse ($ticket->replies as $reply)
                <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium">{{ $reply->user?->name ?? 'Deleted user' }}</p>
                        <p class="text-xs text-gray-400">{{ $reply->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <p class="mt-2 text-sm whitespace-pre-line">{{ $reply->message }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No replies yet.</p>
            @endforelse
        </div>

        @if ($ticket->isOpen())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <form method="POST" action="{{ route('feedback.reply', $ticket) }}">
                    @csrf
                    <x-input-label for="message" value="Add a reply" />
                    <textarea id="message" name="message" rows="4" required
                              class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />

                    <div class="mt-4">
                        <x-primary-button type="submit">Post Reply</x-primary-button>
                    </div>
                </form>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">This ticket is closed.</p>
        @endif
    </div>
</x-app-layout>
