<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Success Stories', 'url' => route('success-stories.index')], ['label' => 'Submit']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Submit Your Story</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Your story will be reviewed by an admin before it's published.</p>
        </div>

        <form method="POST" action="{{ route('success-stories.store') }}" enctype="multipart/form-data" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf

            @include('success-stories._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Submit for Review</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('success-stories.index') }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
