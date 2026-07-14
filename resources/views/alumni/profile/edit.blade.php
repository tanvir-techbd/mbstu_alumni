<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Alumni'], ['label' => 'My Profile']]" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">My Profile</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->completionPercentage() }}% complete</p>
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
                <p class="font-medium">Your verification was rejected</p>
                <p class="mt-1">{{ $profile->rejection_reason }}</p>
                <p class="mt-1">Update your details and re-upload a document below to resubmit.</p>
            </div>
        @endif

        {{-- Profile photo --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-4">Profile Photo</h2>
            <div class="flex items-center gap-4">
                @if (auth()->user()->profile_photo_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->profile_photo_path) }}" class="h-16 w-16 rounded-full object-cover" alt="Profile photo">
                @else
                    <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 text-xl font-semibold">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif

                <form method="POST" action="{{ route('alumni.profile.photo') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                    @csrf
                    <input type="file" name="photo" accept="image/*" class="text-sm">
                    <x-secondary-button type="submit">Upload</x-secondary-button>
                </form>
            </div>
            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
        </div>

        {{-- Verification document --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            <h2 class="text-sm font-semibold mb-1">Verification Document</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Upload your graduation certificate, student ID, or another proof of enrollment. PDF, JPG, or PNG, up to 5MB.</p>

            @if ($profile->verification_document_path)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">A document is on file, submitted {{ $profile->updated_at->diffForHumans() }}.</p>
            @endif

            <form method="POST" action="{{ route('alumni.profile.document') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" class="text-sm">
                <x-secondary-button type="submit">{{ $profile->verification_document_path ? 'Replace & Resubmit' : 'Submit for Verification' }}</x-secondary-button>
            </form>
            <x-input-error :messages="$errors->get('document')" class="mt-2" />
        </div>

        {{-- Profile fields --}}
        <form method="POST" action="{{ route('alumni.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h2 class="text-sm font-semibold mb-4">Personal Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="gender" value="Gender" />
                        <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">—</option>
                            <option value="male" @selected(old('gender', $profile->gender) === 'male')>Male</option>
                            <option value="female" @selected(old('gender', $profile->gender) === 'female')>Female</option>
                            <option value="other" @selected(old('gender', $profile->gender) === 'other')>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="date_of_birth" value="Date of Birth" />
                        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', optional($profile->date_of_birth)->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h2 class="text-sm font-semibold mb-4">Academic Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="student_id" value="Student ID" />
                        <x-text-input id="student_id" name="student_id" class="mt-1 block w-full" :value="old('student_id', $profile->student_id)" />
                        <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="department" value="Department" />
                        <x-text-input id="department" name="department" class="mt-1 block w-full" :value="old('department', $profile->department)" />
                        <x-input-error :messages="$errors->get('department')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="program" value="Program" />
                        <x-text-input id="program" name="program" class="mt-1 block w-full" :value="old('program', $profile->program)" />
                        <x-input-error :messages="$errors->get('program')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="batch" value="Batch" />
                        <x-text-input id="batch" name="batch" class="mt-1 block w-full" :value="old('batch', $profile->batch)" />
                        <x-input-error :messages="$errors->get('batch')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="session" value="Session" />
                        <x-text-input id="session" name="session" class="mt-1 block w-full" :value="old('session', $profile->session)" />
                        <x-input-error :messages="$errors->get('session')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="graduation_year" value="Graduation Year" />
                        <x-text-input id="graduation_year" name="graduation_year" type="number" class="mt-1 block w-full" :value="old('graduation_year', $profile->graduation_year)" />
                        <x-input-error :messages="$errors->get('graduation_year')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="cgpa" value="CGPA (optional)" />
                        <x-text-input id="cgpa" name="cgpa" type="number" step="0.01" class="mt-1 block w-full" :value="old('cgpa', $profile->cgpa)" />
                        <x-input-error :messages="$errors->get('cgpa')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h2 class="text-sm font-semibold mb-4">Professional Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="company" value="Company" />
                        <x-text-input id="company" name="company" class="mt-1 block w-full" :value="old('company', $profile->company)" />
                        <x-input-error :messages="$errors->get('company')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="designation" value="Designation" />
                        <x-text-input id="designation" name="designation" class="mt-1 block w-full" :value="old('designation', $profile->designation)" />
                        <x-input-error :messages="$errors->get('designation')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="industry" value="Industry" />
                        <x-text-input id="industry" name="industry" class="mt-1 block w-full" :value="old('industry', $profile->industry)" />
                        <x-input-error :messages="$errors->get('industry')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="years_of_experience" value="Years of Experience" />
                        <x-text-input id="years_of_experience" name="years_of_experience" type="number" class="mt-1 block w-full" :value="old('years_of_experience', $profile->years_of_experience)" />
                        <x-input-error :messages="$errors->get('years_of_experience')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="country" value="Country" />
                        <x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $profile->country)" />
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="district" value="District" />
                        <x-text-input id="district" name="district" class="mt-1 block w-full" :value="old('district', $profile->district)" />
                        <x-input-error :messages="$errors->get('district')" class="mt-2" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="office_address" value="Office Address" />
                        <textarea id="office_address" name="office_address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('office_address', $profile->office_address) }}</textarea>
                        <x-input-error :messages="$errors->get('office_address')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h2 class="text-sm font-semibold mb-4">Social Links</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="linkedin_url" value="LinkedIn" />
                        <x-text-input id="linkedin_url" name="linkedin_url" type="url" class="mt-1 block w-full" :value="old('linkedin_url', $profile->linkedin_url)" />
                        <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="github_url" value="GitHub" />
                        <x-text-input id="github_url" name="github_url" type="url" class="mt-1 block w-full" :value="old('github_url', $profile->github_url)" />
                        <x-input-error :messages="$errors->get('github_url')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="facebook_url" value="Facebook" />
                        <x-text-input id="facebook_url" name="facebook_url" type="url" class="mt-1 block w-full" :value="old('facebook_url', $profile->facebook_url)" />
                        <x-input-error :messages="$errors->get('facebook_url')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="portfolio_url" value="Portfolio Website" />
                        <x-text-input id="portfolio_url" name="portfolio_url" type="url" class="mt-1 block w-full" :value="old('portfolio_url', $profile->portfolio_url)" />
                        <x-input-error :messages="$errors->get('portfolio_url')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h2 class="text-sm font-semibold mb-4">Additional</h2>
                <div class="space-y-4">
                    <div>
                        <x-input-label for="skills" value="Skills (comma-separated)" />
                        <x-text-input id="skills" name="skills" class="mt-1 block w-full" :value="old('skills', $profile->skills)" placeholder="Laravel, Project Management, Data Analysis" />
                        <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="interests" value="Interests (comma-separated)" />
                        <x-text-input id="interests" name="interests" class="mt-1 block w-full" :value="old('interests', $profile->interests)" />
                        <x-input-error :messages="$errors->get('interests')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="biography" value="Biography" />
                        <textarea id="biography" name="biography" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('biography', $profile->biography) }}</textarea>
                        <x-input-error :messages="$errors->get('biography')" class="mt-2" />
                    </div>
                </div>
            </div>

            <x-primary-button type="submit">Save Profile</x-primary-button>
        </form>
    </div>
</x-app-layout>
