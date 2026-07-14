<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectAlumniProfileRequest;
use App\Models\AlumniProfile;
use App\Services\AlumniProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlumniVerificationController extends Controller
{
    public function __construct(private readonly AlumniProfileService $profiles)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAnyForReview', AlumniProfile::class);

        $status = $request->input('status', VerificationStatus::Pending->value);

        $profiles = AlumniProfile::query()
            ->with('user')
            ->when($status, fn ($query) => $query->where('verification_status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.alumni-verifications.index', [
            'profiles' => $profiles,
            'statuses' => VerificationStatus::cases(),
            'filters' => ['status' => $status],
        ]);
    }

    public function show(AlumniProfile $alumniProfile): View
    {
        $this->authorize('review', $alumniProfile);

        return view('admin.alumni-verifications.show', ['profile' => $alumniProfile]);
    }

    public function downloadDocument(AlumniProfile $alumniProfile): StreamedResponse
    {
        $this->authorize('review', $alumniProfile);

        abort_unless($alumniProfile->verification_document_path, 404);

        return Storage::disk('local')->download($alumniProfile->verification_document_path);
    }

    public function approve(AlumniProfile $alumniProfile, Request $request): RedirectResponse
    {
        $this->authorize('review', $alumniProfile);

        $this->profiles->approve($alumniProfile, $request->user());

        return redirect()->route('admin.alumni-verifications.index')->with('success', 'Alumni verified.');
    }

    public function reject(RejectAlumniProfileRequest $request, AlumniProfile $alumniProfile): RedirectResponse
    {
        $this->profiles->reject($alumniProfile, $request->user(), $request->validated('rejection_reason'));

        return redirect()->route('admin.alumni-verifications.index')->with('success', 'Alumni verification rejected.');
    }
}
