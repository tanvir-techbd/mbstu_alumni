@php
    $document = $document ?? null;
@endphp

<div class="space-y-5 max-w-xl">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $document?->title)" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category" value="Category" />
        <select id="category" name="category" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            @foreach ($categories as $category)
                <option value="{{ $category->value }}" @selected(old('category', $document?->category?->value) === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description (optional)" />
        <textarea id="description" name="description" rows="4"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $document?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="file" :value="$document ? 'Replace File (optional)' : 'File'" />
        @if ($document)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A file is already attached. Uploading a new one will replace it.</p>
        @endif
        <input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" class="mt-1 block w-full text-sm" @required(! $document)>
        <x-input-error :messages="$errors->get('file')" class="mt-2" />
    </div>
</div>
