<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Services\JobPostingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    public function __construct(private readonly JobPostingService $jobs)
    {
    }

    public function index(Request $request): View
    {
        $query = JobPosting::query()->visibleTo($request->user());

        if ($request->boolean('bookmarked')) {
            $query->whereHas('bookmarkedBy', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        $jobs = $query
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(fn ($qq) => $qq->where('position', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%"));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($request->filled('employment_type'), fn ($q) => $q->where('employment_type', $request->string('employment_type')))
            ->when($request->filled('location'), fn ($q) => $q->where('location', 'like', '%'.$request->string('location').'%'))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('jobs.index', [
            'jobs' => $jobs,
            'filters' => $request->only(['search', 'category', 'employment_type', 'location', 'bookmarked']),
        ]);
    }

    public function show(Request $request, JobPosting $job): View
    {
        $this->authorize('view', $job);

        return view('jobs.show', [
            'job' => $job,
            'isBookmarked' => $job->isBookmarkedBy($request->user()),
        ]);
    }

    public function toggleBookmark(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('view', $job);

        $bookmarked = $this->jobs->toggleBookmark($job, $request->user());

        return back()->with('success', $bookmarked ? 'Job bookmarked.' : 'Bookmark removed.');
    }
}
