<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', DocumentType::class);

        $documentTypes = DocumentType::query()
            ->withCount('documents')
            ->when($search = trim((string) $request->query('q')), function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->has('status') && $request->query('status') !== '', fn ($query) => $query->where('is_active', $request->boolean('status')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('document-types.index', compact('documentTypes'));
    }

    public function create(): View
    {
        $this->authorize('create', DocumentType::class);

        return view('document-types.create');
    }

    public function store(StoreDocumentTypeRequest $request): RedirectResponse
    {
        $this->authorize('create', DocumentType::class);

        DocumentType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('document-types.index')
            ->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function edit(DocumentType $documentType): View
    {
        $this->authorize('update', $documentType);

        return view('document-types.edit', compact('documentType'));
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType): RedirectResponse
    {
        $this->authorize('update', $documentType);

        $documentType->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('document-types.index')
            ->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $this->authorize('delete', $documentType);

        if ($documentType->documents()->exists()) {
            return redirect()
                ->route('document-types.index')
                ->with('error', 'Jenis dokumen tidak dapat dihapus karena masih digunakan oleh dokumen.');
        }

        $documentType->delete();

        return redirect()
            ->route('document-types.index')
            ->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}