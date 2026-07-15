@php
    $story = $story ?? null;
@endphp

<div class="space-y-5 max-w-xl">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $story?->title)" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="company" value="Company (optional)" />
            <x-text-input id="company" name="company" class="mt-1 block w-full" :value="old('company', $story?->company)" />
            <x-input-error :messages="$errors->get('company')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="achievement" value="Achievement (optional)" />
            <x-text-input id="achievement" name="achievement" class="mt-1 block w-full" :value="old('achievement', $story?->achievement)" placeholder="Promoted to Senior Engineer" />
            <x-input-error :messages="$errors->get('achievement')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="story" value="Your Story" />
        <textarea id="story" name="story" rows="8" required
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('story', $story?->story) }}</textarea>
        <x-input-error :messages="$errors->get('story')" class="mt-2" />
    </div>

    @if ($story && $story->images->isNotEmpty())
        <div>
            <x-input-label value="Current Images" />
            <div class="mt-2 grid grid-cols-3 gap-2">
                @foreach ($story->images as $image)
                    <div class="relative">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}" class="h-20 w-full object-cover rounded-lg" alt="">
                        <form method="POST" action="{{ route('success-stories.images.destroy', $image) }}" class="absolute top-1 right-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="h-5 w-5 rounded-full bg-black/60 text-white text-xs flex items-center justify-center">×</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <x-input-label for="images" value="Add Images (up to 5, optional)" />
        <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1 block w-full text-sm">
        <x-input-error :messages="$errors->get('images')" class="mt-2" />
        <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
    </div>
</div>
