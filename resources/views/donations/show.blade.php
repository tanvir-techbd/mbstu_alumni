<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Donations', 'url' => route('donations.index')], ['label' => $campaign->title]]" />
    </x-slot>

    <div class="space-y-6 max-w-2xl">
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h1 class="text-xl font-semibold">{{ $campaign->title }}</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $campaign->description }}</p>

            <div class="mt-4">
                <p class="text-2xl font-semibold">৳{{ number_format((float) $campaign->totalRaised(), 2) }}</p>
                @if ($campaign->goal_amount)
                    <p class="text-sm text-gray-400">raised of ৳{{ number_format((float) $campaign->goal_amount, 2) }} goal · {{ $campaign->donorCount() }} donors</p>
                    <div class="mt-2 h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div class="h-full bg-primary-600" style="width: {{ $campaign->progressPercentage() }}%"></div>
                    </div>
                @else
                    <p class="text-sm text-gray-400">raised so far · {{ $campaign->donorCount() }} donors</p>
                @endif
            </div>

            @if ($campaign->status->value === 'closed')
                <p class="mt-6 text-sm text-gray-400">This campaign is closed and no longer accepting donations.</p>
            @else
                <form method="POST" action="{{ route('donations.store', $campaign) }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="amount" value="Amount (BDT)" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="10" class="mt-1 block w-full" required autofocus />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="payment_method" value="Payment Method" />
                        <select id="payment_method" name="payment_method" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                            @foreach (\App\Enums\PaymentMethod::cases() as $method)
                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="transaction_reference" value="Transaction Reference (optional)" />
                        <x-text-input id="transaction_reference" name="transaction_reference" class="mt-1 block w-full" placeholder="bKash TrxID, bank reference…" />
                        <x-input-error :messages="$errors->get('transaction_reference')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-400">Complete your payment via the method above, then record it here. A receipt will be generated immediately.</p>
                    </div>

                    <x-primary-button type="submit">Confirm Donation</x-primary-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
