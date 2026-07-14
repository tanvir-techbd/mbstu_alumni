<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Alumni Verification']]" />
    </x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Alumni Verification</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $profiles->total() }} {{ $filters['status'] }} submissions</p>
        </div>

        <form method="GET" action="{{ route('admin.alumni-verifications.index') }}" class="flex gap-3">
            @foreach ($statuses as $status)
                <a href="{{ route('admin.alumni-verifications.index', ['status' => $status->value]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-medium {{ $filters['status'] === $status->value ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 text-gray-600 dark:text-gray-300' }}">
                    {{ $status->label() }}
                </a>
            @endforeach
        </form>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
            @if ($profiles->isEmpty())
                <x-empty-state title="Nothing here" description="No {{ $filters['status'] }} alumni verification submissions." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="text-left font-medium px-5 py-3">Name</th>
                                <th class="text-left font-medium px-5 py-3">Student ID</th>
                                <th class="text-left font-medium px-5 py-3">Department</th>
                                <th class="text-left font-medium px-5 py-3">Batch</th>
                                <th class="text-left font-medium px-5 py-3">Document</th>
                                <th class="text-right font-medium px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($profiles as $profile)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $profile->user->name }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $profile->student_id ?? '—' }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $profile->department ?? '—' }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $profile->batch ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        @if ($profile->verification_document_path)
                                            <a href="{{ route('admin.alumni-verifications.document', $profile) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Download</a>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.alumni-verifications.show', $profile) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Review</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $profiles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
