@php
    $notice = $notice ?? null;
@endphp

<div class="space-y-5 max-w-xl">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $notice?->title)" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="type" value="Type" />
        <select id="type" name="type" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            @foreach (\App\Enums\NoticeType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $notice?->type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="content" value="Content" />
        <textarea id="content" name="content" rows="6" required
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('content', $notice?->content) }}</textarea>
        <x-input-error :messages="$errors->get('content')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="attachment" value="Attachment (optional)" />
        @if ($notice && $notice->attachment_path)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A file is already attached. Uploading a new one will replace it.</p>
        @endif
        <input id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm">
        <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
    </div>
</div>
