<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Jobs', 'url' => route('jobs.index')], ['label' => 'Post a Job']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Post a Job</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Your posting will be reviewed by an admin before it appears on the job board.</p>
        </div>

        <form method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf

            @include('jobs._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Submit for Review</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('jobs.index') }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
