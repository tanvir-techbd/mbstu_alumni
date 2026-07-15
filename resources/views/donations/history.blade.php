<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Donations', 'url' => route('donations.index')], ['label' => 'My History']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">My Donation History</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $donations->total() }} donations</p>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($donations->isEmpty())
                <x-empty-state title="No donations yet" description="Browse active campaigns and make your first donation." />
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="text-left font-medium px-5 py-3">Campaign</th>
                            <th class="text-left font-medium px-5 py-3">Amount</th>
                            <th class="text-left font-medium px-5 py-3">Method</th>
                            <th class="text-left font-medium px-5 py-3">Date</th>
                            <th class="text-right font-medium px-5 py-3">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($donations as $donation)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $donation->campaign->title }}</td>
                                <td class="px-5 py-3">৳{{ number_format((float) $donation->amount, 2) }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $donation->payment_method->label() }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $donation->donated_at->format('M j, Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('donations.receipt', $donation) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Download</a>
                                </td>
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
