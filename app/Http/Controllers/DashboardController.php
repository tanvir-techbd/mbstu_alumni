<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Enums\MentorshipStatus;
use App\Enums\RoleName;
use App\Enums\VerificationStatus;
use App\Models\AlumniProfile;
use App\Models\Donation;
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
            'totalAlumni' => User::role(RoleName::Alumni->value)->count(),
            'verifiedAlumni' => AlumniProfile::approved()->count(),
            'totalStudents' => User::role(RoleName::Student->value)->count(),
            'totalFaculty' => User::role(RoleName::Faculty->value)->count(),
            'totalEvents' => Event::count(),
            'totalJobs' => JobPosting::count(),
            'totalDonations' => Donation::sum('amount'),
            'pendingVerification' => AlumniProfile::where('verification_status', VerificationStatus::Pending)->count(),
            'monthlyDonations' => $this->monthlyDonationsChartData(),
            'alumniByDepartment' => $this->alumniByDepartmentChartData(),
        ]);
    }

    private function alumniByDepartmentChartData(): array
    {
        $counts = AlumniProfile::approved()
            ->whereNotNull('department')
            ->selectRaw('department, count(*) as total')
            ->groupBy('department')
            ->orderByDesc('total')
            ->pluck('total', 'department');

        return [
            'labels' => $counts->keys()->values()->all(),
            'totals' => $counts->values()->all(),
        ];
    }

    private function monthlyDonationsChartData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $totals = Donation::query()
            ->selectRaw("DATE_FORMAT(donated_at, '%Y-%m') as ym, SUM(amount) as total")
            ->where('donated_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->pluck('total', 'ym');

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->values()->all(),
            'totals' => $months->map(fn ($m) => (float) ($totals[$m->format('Y-m')] ?? 0))->values()->all(),
        ];
    }

    private function alumni(User $user): View
    {
        return view('dashboard.alumni', [
            'profile' => $this->alumniProfiles->ensureProfileExists($user),
            'upcomingEvents' => $this->upcomingEventsCount(),
            'postedJobs' => JobPosting::where('posted_by', $user->id)->count(),
            'pendingMentorshipRequests' => MentorshipRequest::where('mentor_id', $user->id)->where('status', MentorshipStatus::Pending)->count(),
            'totalDonated' => $user->donations()->sum('amount'),
            'donationCount' => $user->donations()->count(),
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
