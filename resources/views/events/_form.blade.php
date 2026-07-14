@php
    $event = $event ?? null;
    $editing = (bool) $event;
@endphp

<div class="space-y-5 max-w-xl">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $event?->title)" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="banner" value="Banner Image" />
        @if ($editing && $event->banner_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($event->banner_path) }}" class="mt-1 h-32 w-full object-cover rounded-lg" alt="Current banner">
        @endif
        <input id="banner" name="banner" type="file" accept="image/*" class="mt-1 block w-full text-sm">
        <x-input-error :messages="$errors->get('banner')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="4" required
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $event?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="venue" value="Venue" />
        <x-text-input id="venue" name="venue" class="mt-1 block w-full" :value="old('venue', $event?->venue)" required />
        <x-input-error :messages="$errors->get('venue')" class="mt-2" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="event_date" value="Date" />
            <x-text-input id="event_date" name="event_date" type="date" class="mt-1 block w-full" :value="old('event_date', $event?->event_date?->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="event_time" value="Time" />
            <x-text-input id="event_time" name="event_time" type="time" class="mt-1 block w-full" :value="old('event_time', $event ? \Illuminate\Support\Carbon::parse($event->event_time)->format('H:i') : null)" required />
            <x-input-error :messages="$errors->get('event_time')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="registration_deadline" value="Registration Deadline" />
            <x-text-input id="registration_deadline" name="registration_deadline" type="datetime-local" class="mt-1 block w-full" :value="old('registration_deadline', $event?->registration_deadline?->format('Y-m-d\TH:i'))" required />
            <x-input-error :messages="$errors->get('registration_deadline')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="capacity" value="Capacity (leave blank for unlimited)" />
            <x-text-input id="capacity" name="capacity" type="number" class="mt-1 block w-full" :value="old('capacity', $event?->capacity)" />
            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
        </div>
    </div>
</div>
