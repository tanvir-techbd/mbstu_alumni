<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Donation Reports']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Donation Reports</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $donations->total() }} donations</p>
            </div>
            <a href="{{ route('admin.donation-campaigns.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Manage Campaigns</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-stat-card label="Total Raised" value="{{ '৳'.number_format((float) $totalRaised, 2) }}" />
            <x-stat-card label="Unique Donors" :value="$totalDonors" />
        </div>

        <form method="GET" action="{{ route('admin.donation-reports.index') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Campaign</label>
                <select name="campaign_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All campaigns</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->id }}" @selected(($filters['campaign_id'] ?? '') == $campaign->id)>{{ $campaign->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Payment Method</label>
                <select name="payment_method" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All methods</option>
                    @foreach (\App\Enums\PaymentMethod::cases() as $method)
                        <option value="{{ $method->value }}" @selected(($filters['payment_method'] ?? '') === $method->value)>{{ $method->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">From</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">To</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <button type="submit" class="rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">Filter</button>
        </form>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($donations->isEmpty())
                <x-empty-state title="No donations found" description="Try a different filter." />
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="text-left font-medium px-5 py-3">Receipt</th>
                            <th class="text-left font-medium px-5 py-3">Donor</th>
                            <th class="text-left font-medium px-5 py-3">Campaign</th>
                            <th class="text-left font-medium px-5 py-3">Amount</th>
                            <th class="text-left font-medium px-5 py-3">Method</th>
                            <th class="text-left font-medium px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($donations as $donation)
                            <tr>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $donation->receipt_number }}</td>
                                <td class="px-5 py-3 font-medium">{{ $donation->user?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $donation->campaign->title }}</td>
                                <td class="px-5 py-3">৳{{ number_format((float) $donation->amount, 2) }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $donation->payment_method->label() }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $donation->donated_at->format('M j, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $donations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
