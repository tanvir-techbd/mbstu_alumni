<?php

namespace App\Services;

use App\Enums\VerificationStatus;
use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AlumniProfileService
{
    public function ensureProfileExists(User $user): AlumniProfile
    {
        return $user->alumniProfile ?? $user->alumniProfile()->create([]);
    }

    public function updateProfile(AlumniProfile $profile, array $data): AlumniProfile
    {
        $profile->fill($data);
        $profile->save();

        return $profile;
    }

    public function uploadProfilePhoto(User $user, UploadedFile $file): void
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->update([
            'profile_photo_path' => $file->store('profile-photos', 'public'),
        ]);
    }

    public function uploadVerificationDocument(AlumniProfile $profile, UploadedFile $file): void
    {
        if ($profile->verification_document_path) {
            Storage::disk('local')->delete($profile->verification_document_path);
        }

        // forceFill: these columns are deliberately absent from $fillable (a user must
        // never mass-assign their own verification status), but this is a trusted,
        // system-controlled write, not user input flowing through mass assignment.
        $profile->forceFill([
            'verification_document_path' => $file->store('verification-documents/'.$profile->user_id, 'local'),
            'verification_status' => VerificationStatus::Pending,
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ])->save();
    }

    public function approve(AlumniProfile $profile, User $reviewer): void
    {
        $profile->forceFill([
            'verification_status' => VerificationStatus::Approved,
            'rejection_reason' => null,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();
    }

    public function reject(AlumniProfile $profile, User $reviewer, string $reason): void
    {
        $profile->forceFill([
            'verification_status' => VerificationStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();
    }
}
