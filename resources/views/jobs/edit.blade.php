<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Jobs', 'url' => route('jobs.index')], ['label' => $job->position, 'url' => route('jobs.show', $job)], ['label' => 'Edit']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Edit Job Posting</h1>

        <form method="POST" action="{{ route('jobs.update', $job) }}" enctype="multipart/form-data" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf
            @method('PUT')

            @include('jobs._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Save Changes</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('jobs.show', $job) }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
