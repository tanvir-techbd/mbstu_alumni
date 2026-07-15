@php
    $campaign = $campaign ?? null;
@endphp

<div class="space-y-5 max-w-xl">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $campaign?->title)" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="5" required
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $campaign?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="goal_amount" value="Goal Amount, BDT (optional)" />
        <x-text-input id="goal_amount" name="goal_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('goal_amount', $campaign?->goal_amount)" />
        <x-input-error :messages="$errors->get('goal_amount')" class="mt-2" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="start_date" value="Start Date (optional)" />
            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $campaign?->start_date?->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="end_date" value="End Date (optional)" />
            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', $campaign?->end_date?->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
        </div>
    </div>
</div>
