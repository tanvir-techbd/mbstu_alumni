<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Documents', 'url' => route('documents.index')], ['label' => 'Upload']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Upload Document</h1>

        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf

            @include('documents._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Upload</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('documents.index') }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
