<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Alumni Directory', 'url' => route('directory.index')], ['label' => $profile->user->name]]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <div class="flex items-center gap-4">
                @if ($profile->user->profile_photo_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->user->profile_photo_path) }}" class="h-20 w-20 rounded-full object-cover" alt="{{ $profile->user->name }}">
                @else
                    <div class="h-20 w-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 text-2xl font-semibold">
                        {{ mb_substr($profile->user->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-semibold">{{ $profile->user->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->designation ?? 'Alumni' }}{{ $profile->company ? ' at '.$profile->company : '' }}</p>
                    <span class="inline-block mt-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 text-xs px-2.5 py-1">Verified Alumni</span>
                </div>
            </div>

            @role('student')
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800" x-data="{ requesting: false }">
                    @if ($hasActiveMentorshipRequest)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            You already have an active mentorship request with this alumni. View it in
                            <a href="{{ route('mentorship.index') }}" class="text-primary-600 dark:text-primary-400 hover:underline">My Mentorship</a>.
                        </p>
                    @else
                        <button type="button" @click="requesting = true" x-show="!requesting" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                            Request Mentorship
                        </button>

                        <form method="POST" action="{{ route('mentorship.request', $profile->user) }}" x-show="requesting" x-cloak class="space-y-3">
                            @csrf
                            <textarea name="message" rows="3" placeholder="Introduce yourself and say what you're hoping to learn (optional)"
                                      class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                            @error('mentorship')
                                <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                            <div class="flex gap-3">
                                <x-primary-button type="submit">Send Request</x-primary-button>
                                <x-secondary-button type="button" @click="requesting = false">Cancel</x-secondary-button>
                            </div>
                        </form>
                    @endif
                </div>
            @endrole

            @if ($profile->biography)
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">{{ $profile->biography }}</p>
            @endif

            @if ($profile->skillList())
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach ($profile->skillList() as $skill)
                        <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2.5 py-1">{{ $skill }}</span>
                    @endforeach
                </div>
            @endif

            @if ($profile->linkedin_url || $profile->github_url || $profile->facebook_url || $profile->portfolio_url)
                <div class="mt-4 flex gap-3 text-sm">
                    @if ($profile->linkedin_url)<a href="{{ $profile->linkedin_url }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">LinkedIn</a>@endif
                    @if ($profile->github_url)<a href="{{ $profile->github_url }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">GitHub</a>@endif
                    @if ($profile->facebook_url)<a href="{{ $profile->facebook_url }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Facebook</a>@endif
                    @if ($profile->portfolio_url)<a href="{{ $profile->portfolio_url }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Portfolio</a>@endif
                </div>
            @endif
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-4">Contact</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-0.5">
                        <a href="mailto:{{ $profile->user->email }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ $profile->user->email }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="mt-0.5">
                        @if ($profile->user->phone)
                            <a href="tel:{{ $profile->user->phone }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ $profile->user->phone }}</a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-4">Academic</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500 dark:text-gray-400">Department</dt><dd class="mt-0.5">{{ $profile->department ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Program</dt><dd class="mt-0.5">{{ $profile->program ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Batch</dt><dd class="mt-0.5">{{ $profile->batch ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Graduation Year</dt><dd class="mt-0.5">{{ $profile->graduation_year ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-4">Professional</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500 dark:text-gray-400">Industry</dt><dd class="mt-0.5">{{ $profile->industry ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Experience</dt><dd class="mt-0.5">{{ $profile->years_of_experience !== null ? $profile->years_of_experience.' years' : '—' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Location</dt><dd class="mt-0.5">{{ $profile->district ?? '—' }}, {{ $profile->country ?? '—' }}</dd></div>
            </dl>
        </div>
    </div>
</x-app-layout>
