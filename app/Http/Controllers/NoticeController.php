<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notice\StoreNoticeRequest;
use App\Http\Requests\Notice\UpdateNoticeRequest;
use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoticeController extends Controller
{
    public function __construct(private readonly NoticeService $notices)
    {
    }

    public function index(Request $request): View
    {
        $query = Notice::query()->with('poster');

        if ($request->boolean('bookmarked')) {
            $query->whereHas('bookmarkedBy', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        $notices = $query
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(fn ($qq) => $qq->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%"));
            })
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('notices.index', [
            'notices' => $notices,
            'filters' => $request->only(['search', 'type', 'bookmarked']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Notice::class);

        return view('notices.create');
    }

    public function store(StoreNoticeRequest $request): RedirectResponse
    {
        $notice = $this->notices->create($request->validated(), $request->user(), $request->file('attachment'));

        return redirect()->route('notices.show', $notice)->with('success', 'Notice published.');
    }

    public function show(Request $request, Notice $notice): View
    {
        return view('notices.show', [
            'notice' => $notice,
            'isBookmarked' => $notice->isBookmarkedBy($request->user()),
        ]);
    }

    public function edit(Notice $notice): View
    {
        $this->authorize('update', $notice);

        return view('notices.edit', ['notice' => $notice]);
    }

    public function update(UpdateNoticeRequest $request, Notice $notice): RedirectResponse
    {
        $this->notices->update($notice, $request->validated(), $request->file('attachment'));

        return redirect()->route('notices.show', $notice)->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $this->authorize('delete', $notice);

        $this->notices->delete($notice);

        return redirect()->route('notices.index')->with('success', 'Notice deleted.');
    }

    public function toggleBookmark(Request $request, Notice $notice): RedirectResponse
    {
        $bookmarked = $this->notices->toggleBookmark($notice, $request->user());

        return back()->with('success', $bookmarked ? 'Notice bookmarked.' : 'Bookmark removed.');
    }

    public function download(Notice $notice): StreamedResponse
    {
        abort_unless($notice->attachment_path, 404);

        return Storage::disk('public')->download($notice->attachment_path);
    }
}
