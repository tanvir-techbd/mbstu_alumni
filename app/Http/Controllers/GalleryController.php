<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gallery\StoreGalleryRequest;
use App\Http\Requests\Gallery\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Services\GalleryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function __construct(private readonly GalleryService $galleries)
    {
    }

    public function index(Request $request): View
    {
        $galleries = Gallery::query()
            ->with('images')
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('gallery.index', [
            'galleries' => $galleries,
            'filters' => $request->only(['category']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Gallery::class);

        return view('gallery.create');
    }

    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $gallery = $this->galleries->create($validated, $request->user(), $images);

        return redirect()->route('gallery.show', $gallery)->with('success', 'Album created.');
    }

    public function show(Gallery $gallery): View
    {
        return view('gallery.show', ['gallery' => $gallery->load('images')]);
    }

    public function edit(Gallery $gallery): View
    {
        $this->authorize('update', $gallery);

        return view('gallery.edit', ['gallery' => $gallery->load('images')]);
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $this->galleries->update($gallery, $validated, $images);

        return redirect()->route('gallery.show', $gallery)->with('success', 'Album updated.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->authorize('delete', $gallery);

        $this->galleries->delete($gallery);

        return redirect()->route('gallery.index')->with('success', 'Album deleted.');
    }

    public function destroyImage(GalleryImage $image): RedirectResponse
    {
        $this->authorize('update', $image->gallery);

        $this->galleries->deleteImage($image);

        return back()->with('success', 'Image removed.');
    }
}
