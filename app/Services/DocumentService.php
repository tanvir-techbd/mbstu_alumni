<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function create(array $data, User $uploader, UploadedFile $file): Document
    {
        $document = new Document($data);
        $document->uploaded_by = $uploader->id;
        $document->file_path = $file->store('documents', 'local');
        $document->file_size = $file->getSize();
        $document->save();

        return $document;
    }

    public function update(Document $document, array $data, ?UploadedFile $file = null): Document
    {
        $document->fill($data);

        if ($file) {
            Storage::disk('local')->delete($document->file_path);
            $document->file_path = $file->store('documents', 'local');
            $document->file_size = $file->getSize();
        }

        $document->save();

        return $document;
    }

    public function delete(Document $document): void
    {
        Storage::disk('local')->delete($document->file_path);

        $document->delete();
    }
}
