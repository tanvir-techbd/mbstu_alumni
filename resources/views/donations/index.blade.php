<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Donations']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Donation Campaigns</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Support MBSTU alumni initiatives.</p>
            </div>
            <a href="{{ route('donations.history') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">My Donation History</a>
        </div>

        @if ($campaigns->isEmpty())
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-empty-state title="No active campaigns" description="Check back later." />
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($campaigns as $campaign)
                    <a href="{{ route('donations.show', $campaign) }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5 hover:ring-primary-500 transition">
                        <p class="font-medium">{{ $campaign->title }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $campaign->description }}</p>

                        <div class="mt-4">
                            <p class="text-lg font-semibold">৳{{ number_format((float) $campaign->donations_sum_amount ?: 0, 2) }}</p>
                            @if ($campaign->goal_amount)
                                <p class="text-xs text-gray-400">raised of ৳{{ number_format((float) $campaign->goal_amount, 2) }} goal</p>
                                @php
                                    $progress = min(100, round(((float) ($campaign->donations_sum_amount ?: 0) / (float) $campaign->goal_amount) * 100));
                                @endphp
                                <div class="mt-2 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <div class="h-full bg-primary-600" style="width: {{ $progress }}%"></div>
                                </div>
                            @else
                                <p class="text-xs text-gray-400">raised so far</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div>{{ $campaigns->links() }}</div>
        @endif
    </div>
</x-app-layout>
