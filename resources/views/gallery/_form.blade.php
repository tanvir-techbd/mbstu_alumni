@php
    $gallery = $gallery ?? null;
@endphp

<div class="space-y-5 max-w-xl">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $gallery?->title)" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category" value="Category" />
        <select id="category" name="category" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">
            @foreach (\App\Enums\GalleryCategory::cases() as $category)
                <option value="{{ $category->value }}" @selected(old('category', $gallery?->category?->value) === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description (optional)" />
        <textarea id="description" name="description" rows="3"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $gallery?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    @if ($gallery && $gallery->images->isNotEmpty())
        <div>
            <x-input-label value="Current Photos" />
            <div class="mt-2 grid grid-cols-4 gap-2">
                @foreach ($gallery->images as $image)
                    <div class="relative">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}" class="h-20 w-full object-cover rounded-lg" alt="">
                        <form method="POST" action="{{ route('gallery.images.destroy', $image) }}" class="absolute top-1 right-1">
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
        <x-input-label for="images" value="Add Photos (up to 20, optional)" />
        <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1 block w-full text-sm">
        <x-input-error :messages="$errors->get('images')" class="mt-2" />
        <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
    </div>
</div>
