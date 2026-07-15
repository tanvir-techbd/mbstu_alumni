<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCategory;
use App\Http\Requests\Document\StoreDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documents)
    {
    }

    public function index(Request $request): View
    {
        $documents = Document::query()
            ->with('uploader')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('documents.index', [
            'documents' => $documents,
            'filters' => $request->only(['search', 'category']),
            'categories' => DocumentCategory::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Document::class);

        return view('documents.create', ['categories' => DocumentCategory::cases()]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $this->documents->create($request->validated(), $request->user(), $request->file('file'));

        return redirect()->route('documents.index')->with('success', 'Document uploaded.');
    }

    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        return view('documents.edit', ['document' => $document, 'categories' => DocumentCategory::cases()]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->documents->update($document, $request->validated(), $request->file('file'));

        return redirect()->route('documents.index')->with('success', 'Document updated.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $this->documents->delete($document);

        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    public function download(Document $document): StreamedResponse
    {
        return Storage::disk('local')->download($document->file_path, $document->title.'.'.pathinfo($document->file_path, PATHINFO_EXTENSION));
    }
}
