<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Admin'], ['label' => 'Alumni Verification', 'url' => route('admin.alumni-verifications.index')], ['label' => $profile->user->name]]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ $profile->user->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->user->email }}</p>
            </div>

            @php
                $statusTone = match ($profile->verification_status) {
                    \App\Enums\VerificationStatus::Approved => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                    \App\Enums\VerificationStatus::Rejected => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
                    default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                };
            @endphp
            <span class="rounded-full text-xs px-3 py-1.5 font-medium {{ $statusTone }}">{{ $profile->verification_status->label() }}</span>
        </div>

        @if ($profile->verification_status === \App\Enums\VerificationStatus::Rejected && $profile->rejection_reason)
            <div class="rounded-xl bg-rose-50 dark:bg-rose-500/10 ring-1 ring-rose-200 dark:ring-rose-500/20 p-4 text-sm text-rose-700 dark:text-rose-400">
                <p class="font-medium">Previously rejected</p>
                <p class="mt-1">{{ $profile->rejection_reason }}</p>
            </div>
        @endif

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-4">Verification Document</h2>
            @if ($profile->verification_document_path)
                <a href="{{ route('admin.alumni-verifications.document', $profile) }}" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:underline text-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                    Download document
                </a>
            @else
                <p class="text-sm text-gray-400">No document uploaded yet.</p>
            @endif
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-4">Academic Information</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500 dark:text-gray-400">Student ID</dt><dd class="mt-0.5">{{ $profile->student_id ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Department</dt><dd class="mt-0.5">{{ $profile->department ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Program</dt><dd class="mt-0.5">{{ $profile->program ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Batch</dt><dd class="mt-0.5">{{ $profile->batch ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Session</dt><dd class="mt-0.5">{{ $profile->session ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Graduation Year</dt><dd class="mt-0.5">{{ $profile->graduation_year ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">CGPA</dt><dd class="mt-0.5">{{ $profile->cgpa ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-4">Professional Information</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500 dark:text-gray-400">Company</dt><dd class="mt-0.5">{{ $profile->company ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Designation</dt><dd class="mt-0.5">{{ $profile->designation ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Industry</dt><dd class="mt-0.5">{{ $profile->industry ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Country / District</dt><dd class="mt-0.5">{{ $profile->country ?? '—' }} / {{ $profile->district ?? '—' }}</dd></div>
            </dl>
        </div>

        @if ($profile->verification_status !== \App\Enums\VerificationStatus::Approved)
            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6" x-data="{ rejecting: false }">
                <h2 class="text-sm font-semibold mb-4">Decision</h2>

                <div class="flex gap-3" x-show="!rejecting">
                    <form method="POST" action="{{ route('admin.alumni-verifications.approve', $profile) }}">
                        @csrf
                        <x-primary-button type="submit">Approve</x-primary-button>
                    </form>
                    <x-secondary-button type="button" @click="rejecting = true">Reject</x-secondary-button>
                </div>

                <form x-show="rejecting" method="POST" action="{{ route('admin.alumni-verifications.reject', $profile) }}" x-cloak>
                    @csrf
                    <x-input-label for="rejection_reason" value="Reason for rejection" />
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                              class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('rejection_reason') }}</textarea>
                    <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />

                    <div class="mt-3 flex gap-3">
                        <x-danger-button type="submit">Confirm Rejection</x-danger-button>
                        <x-secondary-button type="button" @click="rejecting = false">Cancel</x-secondary-button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
