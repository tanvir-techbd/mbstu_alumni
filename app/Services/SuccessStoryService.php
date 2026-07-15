<?php

namespace App\Services;

use App\Enums\SuccessStoryStatus;
use App\Models\SuccessStory;
use App\Models\SuccessStoryImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SuccessStoryService
{
    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     */
    public function create(array $data, User $submitter, array $images = []): SuccessStory
    {
        $story = new SuccessStory($data);
        $story->user_id = $submitter->id;
        $story->forceFill(['status' => SuccessStoryStatus::Pending]);
        $story->save();

        $this->attachImages($story, $images);

        return $story;
    }

    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     */
    public function update(SuccessStory $story, array $data, array $images = []): SuccessStory
    {
        $story->fill($data);
        $story->save();

        $this->attachImages($story, $images);

        return $story;
    }

    public function approve(SuccessStory $story, User $reviewer): void
    {
        $story->forceFill([
            'status' => SuccessStoryStatus::Published,
            'rejection_reason' => null,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();
    }

    public function reject(SuccessStory $story, User $reviewer, string $reason): void
    {
        $story->forceFill([
            'status' => SuccessStoryStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();
    }

    public function delete(SuccessStory $story): void
    {
        $story->images->each(fn (SuccessStoryImage $image) => Storage::disk('public')->delete($image->image_path));

        $story->delete();
    }

    public function deleteImage(SuccessStoryImage $image): void
    {
        Storage::disk('public')->delete($image->image_path);

        $image->delete();
    }

    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     */
    private function attachImages(SuccessStory $story, array $images): void
    {
        foreach ($images as $image) {
            $story->images()->create([
                'image_path' => $image->store('success-story-images', 'public'),
            ]);
        }
    }
}
