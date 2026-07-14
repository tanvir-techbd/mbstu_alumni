<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alumni\UpdateAlumniProfileRequest;
use App\Http\Requests\Alumni\UploadProfilePhotoRequest;
use App\Http\Requests\Alumni\UploadVerificationDocumentRequest;
use App\Services\AlumniProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlumniProfileController extends Controller
{
    public function __construct(private readonly AlumniProfileService $profiles)
    {
    }

    public function edit(Request $request): View
    {
        $profile = $this->profiles->ensureProfileExists($request->user());

        $this->authorize('update', $profile);

        return view('alumni.profile.edit', ['profile' => $profile]);
    }

    public function update(UpdateAlumniProfileRequest $request): RedirectResponse
    {
        $profile = $this->profiles->ensureProfileExists($request->user());

        $this->profiles->updateProfile($profile, $request->validated());

        return redirect()->route('alumni.profile.edit')->with('success', 'Profile updated.');
    }

    public function uploadPhoto(UploadProfilePhotoRequest $request): RedirectResponse
    {
        $this->profiles->uploadProfilePhoto($request->user(), $request->file('photo'));

        return redirect()->route('alumni.profile.edit')->with('success', 'Profile photo updated.');
    }

    public function uploadDocument(UploadVerificationDocumentRequest $request): RedirectResponse
    {
        $profile = $this->profiles->ensureProfileExists($request->user());

        $this->profiles->uploadVerificationDocument($profile, $request->file('document'));

        return redirect()->route('alumni.profile.edit')->with('success', 'Verification document submitted for review.');
    }
}
