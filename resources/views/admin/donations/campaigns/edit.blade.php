<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Donation Campaigns', 'url' => route('admin.donation-campaigns.index')], ['label' => 'Edit']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Edit Donation Campaign</h1>

        <form method="POST" action="{{ route('admin.donation-campaigns.update', $campaign) }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf
            @method('PUT')

            @include('admin.donations.campaigns._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Save Changes</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('admin.donation-campaigns.index') }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
