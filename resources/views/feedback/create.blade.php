<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => 'Feedback', 'url' => route('feedback.index')], ['label' => 'Submit']]" />
    </x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold">Submit Feedback</h1>

        <form method="POST" action="{{ route('feedback.store') }}" class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
            @csrf

            <div class="space-y-5 max-w-xl">
                <div>
                    <x-input-label for="type" value="Type" />
                    <select id="type" name="type" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="subject" value="Subject" />
                    <x-text-input id="subject" name="subject" class="mt-1 block w-full" :value="old('subject')" required autofocus />
                    <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="message" value="Message" />
                    <textarea id="message" name="message" rows="6" required
                              class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <x-primary-button type="submit">Submit</x-primary-button>
                <x-secondary-button type="button" onclick="window.location='{{ route('feedback.index') }}'">Cancel</x-secondary-button>
            </div>
        </form>
    </div>
</x-app-layout>
