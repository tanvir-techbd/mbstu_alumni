<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Donation Campaigns']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Donation Campaigns</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $campaigns->total() }} campaigns</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.donation-reports.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline self-center">View Reports</a>
                <a href="{{ route('admin.donation-campaigns.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                    New Campaign
                </a>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($campaigns->isEmpty())
                <x-empty-state title="No campaigns yet" />
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="text-left font-medium px-5 py-3">Title</th>
                            <th class="text-left font-medium px-5 py-3">Status</th>
                            <th class="text-left font-medium px-5 py-3">Raised</th>
                            <th class="text-left font-medium px-5 py-3">Donations</th>
                            <th class="text-right font-medium px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($campaigns as $campaign)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $campaign->title }}</td>
                                <td class="px-5 py-3">
                                    @if ($campaign->status->value === 'active')
                                        <span class="rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 text-xs px-2.5 py-1">Active</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 text-xs px-2.5 py-1">Closed</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">৳{{ number_format((float) ($campaign->donations_sum_amount ?: 0), 2) }}{{ $campaign->goal_amount ? ' / ৳'.number_format((float) $campaign->goal_amount, 2) : '' }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $campaign->donations_count }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.donation-campaigns.edit', $campaign) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Edit</a>

                                        @if ($campaign->status->value === 'active')
                                            <form method="POST" action="{{ route('admin.donation-campaigns.close', $campaign) }}">
                                                @csrf
                                                <button type="submit" class="text-gray-500 dark:text-gray-400 hover:underline">Close</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.donation-campaigns.reopen', $campaign) }}">
                                                @csrf
                                                <button type="submit" class="text-emerald-600 dark:text-emerald-400 hover:underline">Reopen</button>
                                            </form>
                                        @endif

                                        @if ($campaign->donations_count === 0)
                                            <form method="POST" action="{{ route('admin.donation-campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this campaign?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
