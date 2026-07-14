<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NoticeService
{
    public function create(array $data, User $poster, ?UploadedFile $attachment = null): Notice
    {
        $notice = new Notice($data);
        $notice->posted_by = $poster->id;

        if ($attachment) {
            $notice->attachment_path = $attachment->store('notice-attachments', 'public');
        }

        $notice->save();

        return $notice;
    }

    public function update(Notice $notice, array $data, ?UploadedFile $attachment = null): Notice
    {
        $notice->fill($data);

        if ($attachment) {
            if ($notice->attachment_path) {
                Storage::disk('public')->delete($notice->attachment_path);
            }
            $notice->attachment_path = $attachment->store('notice-attachments', 'public');
        }

        $notice->save();

        return $notice;
    }

    public function delete(Notice $notice): void
    {
        if ($notice->attachment_path) {
            Storage::disk('public')->delete($notice->attachment_path);
        }

        $notice->delete();
    }

    public function toggleBookmark(Notice $notice, User $user): bool
    {
        if ($notice->isBookmarkedBy($user)) {
            $notice->bookmarkedBy()->detach($user->id);

            return false;
        }

        $notice->bookmarkedBy()->attach($user->id);

        return true;
    }
}
