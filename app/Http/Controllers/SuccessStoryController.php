<?php

namespace App\Http\Controllers;

use App\Models\SuccessStory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SuccessStoryController extends Controller
{
    public function index(Request $request): View
    {
        $stories = SuccessStory::query()
            ->visibleTo($request->user())
            ->with(['user', 'images'])
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('success-stories.index', ['stories' => $stories]);
    }

    public function show(SuccessStory $successStory): View
    {
        $this->authorize('view', $successStory);

        return view('success-stories.show', [
            'story' => $successStory->load(['user', 'images']),
        ]);
    }
}
