<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Events', 'url' => route('events.index')], ['label' => 'Create']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Create Event</h1>

        <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf

            @include('events._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Create Event</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('events.index') }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
