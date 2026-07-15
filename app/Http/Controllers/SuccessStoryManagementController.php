<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuccessStory\RejectSuccessStoryRequest;
use App\Http\Requests\SuccessStory\StoreSuccessStoryRequest;
use App\Http\Requests\SuccessStory\UpdateSuccessStoryRequest;
use App\Models\SuccessStory;
use App\Models\SuccessStoryImage;
use App\Services\SuccessStoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SuccessStoryManagementController extends Controller
{
    public function __construct(private readonly SuccessStoryService $stories)
    {
    }

    public function create(): View
    {
        $this->authorize('create', SuccessStory::class);

        return view('success-stories.create');
    }

    public function store(StoreSuccessStoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $story = $this->stories->create($validated, $request->user(), $images);

        return redirect()->route('success-stories.show', $story)->with('success', 'Your story was submitted for review.');
    }

    public function edit(SuccessStory $successStory): View
    {
        $this->authorize('update', $successStory);

        return view('success-stories.edit', ['story' => $successStory->load('images')]);
    }

    public function update(UpdateSuccessStoryRequest $request, SuccessStory $successStory): RedirectResponse
    {
        $validated = $request->validated();
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $this->stories->update($successStory, $validated, $images);

        return redirect()->route('success-stories.show', $successStory)->with('success', 'Story updated.');
    }

    public function destroy(SuccessStory $successStory): RedirectResponse
    {
        $this->authorize('delete', $successStory);

        $this->stories->delete($successStory);

        return redirect()->route('success-stories.index')->with('success', 'Story deleted.');
    }

    public function destroyImage(SuccessStoryImage $image): RedirectResponse
    {
        $this->authorize('update', $image->successStory);

        $this->stories->deleteImage($image);

        return back()->with('success', 'Image removed.');
    }

    public function approve(Request $request, SuccessStory $successStory): RedirectResponse
    {
        $this->authorize('review', SuccessStory::class);

        $this->stories->approve($successStory, $request->user());

        return back()->with('success', 'Story published.');
    }

    public function reject(RejectSuccessStoryRequest $request, SuccessStory $successStory): RedirectResponse
    {
        $this->stories->reject($successStory, $request->user(), $request->validated('rejection_reason'));

        return back()->with('success', 'Story rejected.');
    }
}
