<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Events', 'url' => route('events.index')], ['label' => $event->title, 'url' => route('events.show', $event)], ['label' => 'Edit']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Edit Event</h1>

        <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf
            @method('PUT')

            @include('events._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Save Changes</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('events.show', $event) }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
