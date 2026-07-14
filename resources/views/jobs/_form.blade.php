@php
    $job = $job ?? null;
    $editing = (bool) $job;
@endphp

<div class="space-y-5 max-w-xl">
    <div>
        <x-input-label for="company" value="Company" />
        <x-text-input id="company" name="company" class="mt-1 block w-full" :value="old('company', $job?->company)" required autofocus />
        <x-input-error :messages="$errors->get('company')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="company_logo" value="Company Logo" />
        @if ($editing && $job->company_logo_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($job->company_logo_path) }}" class="mt-1 h-16 w-16 rounded-lg object-cover" alt="Current logo">
        @endif
        <input id="company_logo" name="company_logo" type="file" accept="image/*" class="mt-1 block w-full text-sm">
        <x-input-error :messages="$errors->get('company_logo')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="position" value="Position" />
        <x-text-input id="position" name="position" class="mt-1 block w-full" :value="old('position', $job?->position)" required />
        <x-input-error :messages="$errors->get('position')" class="mt-2" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="category" value="Category" />
            <x-text-input id="category" name="category" class="mt-1 block w-full" :value="old('category', $job?->category)" required placeholder="Engineering, Marketing…" />
            <x-input-error :messages="$errors->get('category')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="employment_type" value="Employment Type" />
            <select id="employment_type" name="employment_type" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                @foreach (\App\Enums\EmploymentType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('employment_type', $job?->employment_type?->value) === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('employment_type')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="salary" value="Salary (optional)" />
            <x-text-input id="salary" name="salary" class="mt-1 block w-full" :value="old('salary', $job?->salary)" placeholder="Negotiable, 50k–70k BDT…" />
            <x-input-error :messages="$errors->get('salary')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="experience" value="Experience (optional)" />
            <x-text-input id="experience" name="experience" class="mt-1 block w-full" :value="old('experience', $job?->experience)" placeholder="2–4 years" />
            <x-input-error :messages="$errors->get('experience')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="location" value="Location" />
            <x-text-input id="location" name="location" class="mt-1 block w-full" :value="old('location', $job?->location)" required />
            <x-input-error :messages="$errors->get('location')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="deadline" value="Application Deadline" />
            <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline', $job?->deadline?->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="4" required
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $job?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="apply_url" value="Apply URL" />
        <x-text-input id="apply_url" name="apply_url" type="url" class="mt-1 block w-full" :value="old('apply_url', $job?->apply_url)" required placeholder="https://…" />
        <x-input-error :messages="$errors->get('apply_url')" class="mt-2" />
    </div>
</div>
