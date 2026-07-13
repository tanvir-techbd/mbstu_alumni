<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Users', 'url' => route('admin.users.index')], ['label' => 'Add User']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Add User</h1>

        <form method="POST" action="{{ route('admin.users.store') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf

            @include('admin.users._form')

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Create User</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('admin.users.index') }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
