<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Users']]" />
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Users</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $users->total() }} total</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                Add User
            </a>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name or email…"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Role</label>
                <select name="role" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(($filters['role'] ?? '') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All statuses</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium px-4 py-2">Filter</button>
                @if (($filters['search'] ?? '') || ($filters['role'] ?? '') || ($filters['status'] ?? ''))
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium px-4 py-2">Clear</a>
                @endif
            </div>
        </form>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($users->isEmpty())
                <x-empty-state title="No users found" description="Try a different search or filter." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="text-left font-medium px-5 py-3">Name</th>
                                <th class="text-left font-medium px-5 py-3">Email</th>
                                <th class="text-left font-medium px-5 py-3">Role</th>
                                <th class="text-left font-medium px-5 py-3">Status</th>
                                <th class="text-left font-medium px-5 py-3">Joined</th>
                                <th class="text-right font-medium px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $user->name }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $user->roles->first()?->name ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        @if ($user->status === 'active')
                                            <span class="rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 text-xs px-2.5 py-1">Active</span>
                                        @else
                                            <span class="rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 text-xs px-2.5 py-1">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $user->created_at->format('M j, Y') }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Edit</a>

                                            @can('toggleStatus', $user)
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-gray-500 dark:text-gray-400 hover:underline">
                                                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('delete', $user)
                                                <button
                                                    type="button"
                                                    x-data=""
                                                    x-on:click="$dispatch('open-modal', 'confirm-delete-user-{{ $user->id }}')"
                                                    class="text-rose-600 dark:text-rose-400 hover:underline"
                                                >Delete</button>

                                                <x-modal :name="'confirm-delete-user-' . $user->id" maxWidth="md">
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="p-6">
                                                        @csrf
                                                        @method('DELETE')

                                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete {{ $user->name }}?</h2>
                                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">This permanently deletes the account. This cannot be undone.</p>

                                                        <div class="mt-6 flex justify-end gap-3">
                                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                                            <x-danger-button type="submit">Delete</x-danger-button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
