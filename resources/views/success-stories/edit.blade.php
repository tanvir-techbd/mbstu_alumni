<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Success Stories', 'url' => route('success-stories.index')], ['label' => $story->title, 'url' => route('success-stories.show', $story)], ['label' => 'Edit']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Edit Story</h1>

        <form method="POST" action="{{ route('success-stories.update', $story) }}" enctype="multipart/form-data" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf
            @method('PUT')

            @include('success-stories._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Save Changes</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('success-stories.show', $story) }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
