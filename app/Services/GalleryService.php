<?php

namespace App\Services;

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class GalleryService
{
    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     */
    public function create(array $data, User $creator, array $images = []): Gallery
    {
        $gallery = new Gallery($data);
        $gallery->created_by = $creator->id;
        $gallery->save();

        $this->attachImages($gallery, $images);

        return $gallery;
    }

    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     */
    public function update(Gallery $gallery, array $data, array $images = []): Gallery
    {
        $gallery->fill($data);
        $gallery->save();

        $this->attachImages($gallery, $images);

        return $gallery;
    }

    public function delete(Gallery $gallery): void
    {
        $gallery->images->each(fn (GalleryImage $image) => Storage::disk('public')->delete($image->image_path));

        $gallery->delete();
    }

    public function deleteImage(GalleryImage $image): void
    {
        Storage::disk('public')->delete($image->image_path);

        $image->delete();
    }

    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     */
    private function attachImages(Gallery $gallery, array $images): void
    {
        foreach ($images as $image) {
            $gallery->images()->create([
                'image_path' => $image->store('gallery-images', 'public'),
            ]);
        }
    }
}
