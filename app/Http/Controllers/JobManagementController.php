<?php

namespace App\Http\Controllers;

use App\Http\Requests\Job\RejectJobPostingRequest;
use App\Http\Requests\Job\StoreJobPostingRequest;
use App\Http\Requests\Job\UpdateJobPostingRequest;
use App\Models\JobPosting;
use App\Services\JobPostingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobManagementController extends Controller
{
    public function __construct(private readonly JobPostingService $jobs)
    {
    }

    public function create(): View
    {
        $this->authorize('create', JobPosting::class);

        return view('jobs.create');
    }

    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        $job = $this->jobs->create($request->validated(), $request->user(), $request->file('company_logo'));

        return redirect()->route('jobs.show', $job)->with('success', 'Job posted. It will be reviewed by an admin before it goes live.');
    }

    public function edit(JobPosting $job): View
    {
        $this->authorize('update', $job);

        return view('jobs.edit', ['job' => $job]);
    }

    public function update(UpdateJobPostingRequest $request, JobPosting $job): RedirectResponse
    {
        $this->jobs->update($job, $request->validated(), $request->file('company_logo'));

        return redirect()->route('jobs.show', $job)->with('success', 'Job posting updated.');
    }

    public function destroy(JobPosting $job): RedirectResponse
    {
        $this->authorize('delete', $job);

        $this->jobs->delete($job);

        return redirect()->route('jobs.index')->with('success', 'Job posting deleted.');
    }

    public function approve(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('review', JobPosting::class);

        $this->jobs->approve($job, $request->user());

        return back()->with('success', 'Job published.');
    }

    public function reject(RejectJobPostingRequest $request, JobPosting $job): RedirectResponse
    {
        $this->jobs->reject($job, $request->user(), $request->validated('rejection_reason'));

        return back()->with('success', 'Job posting rejected.');
    }
}
