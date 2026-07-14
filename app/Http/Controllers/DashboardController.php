<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Enums\MentorshipStatus;
use App\Enums\RoleName;
use App\Enums\VerificationStatus;
use App\Models\AlumniProfile;
use App\Models\Event;
use App\Models\JobPosting;
use App\Models\MentorshipRequest;
use App\Models\Notice;
use App\Models\User;
use App\Services\AlumniProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    public function __construct(private readonly AlumniProfileService $alumniProfiles)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        return match (true) {
            $user->hasRole(RoleName::SuperAdmin->value) => $this->admin(),
            $user->hasRole(RoleName::Alumni->value) => $this->alumni($user),
            $user->hasRole(RoleName::Student->value) => $this->student($user),
            $user->hasRole(RoleName::Faculty->value) => $this->faculty($user),
            default => throw new HttpException(403, 'Your account has no role assigned yet. Contact an administrator.'),
        };
    }

    private function admin(): View
    {
        return view('dashboard.admin', [
            'totalUsers' => User::count(),
            'totalAlumni' => User::role(RoleName::Alumni->value)->count(),
            'totalStudents' => User::role(RoleName::Student->value)->count(),
            'totalFaculty' => User::role(RoleName::Faculty->value)->count(),
            'verifiedAlumni' => AlumniProfile::approved()->count(),
            'pendingVerification' => AlumniProfile::where('verification_status', VerificationStatus::Pending)->count(),
            'totalEvents' => Event::count(),
        ]);
    }

    private function alumni(User $user): View
    {
        return view('dashboard.alumni', [
            'profile' => $this->alumniProfiles->ensureProfileExists($user),
            'upcomingEvents' => $this->upcomingEventsCount(),
            'postedJobs' => JobPosting::where('posted_by', $user->id)->count(),
            'pendingMentorshipRequests' => MentorshipRequest::where('mentor_id', $user->id)->where('status', MentorshipStatus::Pending)->count(),
        ]);
    }

    private function student(User $user): View
    {
        return view('dashboard.student', [
            'upcomingEvents' => $this->upcomingEventsCount(),
            'savedJobs' => $user->bookmarkedJobs()->count(),
            'mentorshipRequests' => MentorshipRequest::where('student_id', $user->id)->count(),
            'totalNotices' => Notice::count(),
        ]);
    }

    private function faculty(User $user): View
    {
        return view('dashboard.faculty', [
            'publishedEvents' => Event::where('created_by', $user->id)->where('status', EventStatus::Published)->count(),
            'verifiedAlumni' => AlumniProfile::approved()->count(),
            'postedNotices' => Notice::where('posted_by', $user->id)->count(),
        ]);
    }

    private function upcomingEventsCount(): int
    {
        return Event::published()->where('event_date', '>=', now()->toDateString())->count();
    }
}
