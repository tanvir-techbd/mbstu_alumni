<?php

namespace App\Services;

use App\Enums\JobStatus;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class JobPostingService
{
    public function create(array $data, User $poster, ?UploadedFile $logo = null): JobPosting
    {
        $job = new JobPosting($data);
        $job->posted_by = $poster->id;
        $job->forceFill(['status' => JobStatus::Pending]);

        if ($logo) {
            $job->company_logo_path = $logo->store('job-logos', 'public');
        }

        $job->save();

        return $job;
    }

    public function update(JobPosting $job, array $data, ?UploadedFile $logo = null): JobPosting
    {
        $job->fill($data);

        if ($logo) {
            if ($job->company_logo_path) {
                Storage::disk('public')->delete($job->company_logo_path);
            }
            $job->company_logo_path = $logo->store('job-logos', 'public');
        }

        $job->save();

        return $job;
    }

    public function approve(JobPosting $job, User $reviewer): void
    {
        $job->forceFill([
            'status' => JobStatus::Published,
            'rejection_reason' => null,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();
    }

    public function reject(JobPosting $job, User $reviewer, string $reason): void
    {
        $job->forceFill([
            'status' => JobStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();
    }

    public function delete(JobPosting $job): void
    {
        if ($job->company_logo_path) {
            Storage::disk('public')->delete($job->company_logo_path);
        }

        $job->delete();
    }

    public function toggleBookmark(JobPosting $job, User $user): bool
    {
        if ($job->isBookmarkedBy($user)) {
            $job->bookmarkedBy()->detach($user->id);

            return false;
        }

        $job->bookmarkedBy()->attach($user->id);

        return true;
    }
}
