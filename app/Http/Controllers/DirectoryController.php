<?php

namespace App\Http\Controllers;

use App\Enums\MentorshipStatus;
use App\Enums\RoleName;
use App\Enums\VerificationStatus;
use App\Models\AlumniProfile;
use App\Models\MentorshipRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $profiles = AlumniProfile::query()
            ->approved()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(fn ($q) => $q->where('student_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")));
            })
            ->when($request->filled('department'), fn ($query) => $query->where('department', $request->string('department')))
            ->when($request->filled('batch'), fn ($query) => $query->where('batch', $request->string('batch')))
            ->when($request->filled('session'), fn ($query) => $query->where('session', $request->string('session')))
            ->when($request->filled('graduation_year'), fn ($query) => $query->where('graduation_year', $request->integer('graduation_year')))
            ->when($request->filled('company'), fn ($query) => $query->where('company', 'like', '%'.$request->string('company').'%'))
            ->when($request->filled('country'), fn ($query) => $query->where('country', $request->string('country')))
            ->when($request->filled('district'), fn ($query) => $query->where('district', $request->string('district')))
            ->when($request->filled('skills'), fn ($query) => $query->where('skills', 'like', '%'.$request->string('skills').'%'))
            ->tap(function ($query) use ($request) {
                match ($request->input('sort', 'latest')) {
                    'name' => $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'alumni_profiles.user_id')
                    ),
                    'graduation_year' => $query->orderByDesc('graduation_year'),
                    default => $query->latest('alumni_profiles.created_at'),
                };
            })
            ->paginate(12)
            ->withQueryString();

        return view('directory.index', [
            'profiles' => $profiles,
            'filters' => $request->only([
                'search', 'department', 'batch', 'session', 'graduation_year',
                'company', 'country', 'district', 'skills', 'sort',
            ]),
        ]);
    }

    public function show(Request $request, AlumniProfile $alumniProfile): View
    {
        abort_unless($alumniProfile->verification_status === VerificationStatus::Approved, 404);

        $hasActiveMentorshipRequest = false;

        if ($request->user()->hasRole(RoleName::Student->value)) {
            $hasActiveMentorshipRequest = MentorshipRequest::where('student_id', $request->user()->id)
                ->where('mentor_id', $alumniProfile->user_id)
                ->whereIn('status', [MentorshipStatus::Pending, MentorshipStatus::Accepted])
                ->exists();
        }

        return view('directory.show', [
            'profile' => $alumniProfile,
            'hasActiveMentorshipRequest' => $hasActiveMentorshipRequest,
        ]);
    }
}
