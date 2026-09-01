<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Stage;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $service)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');

        $allowedSorts = ['created_at', 'document_date', 'year', 'title'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';

        $documents = Document::query()
            ->with(['category:id,name', 'documentType:id,name', 'stage:id,name'])
            ->when($search = trim((string) $request->query('q')), function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('year'), fn ($query) => $query->where('year', $request->query('year')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->query('category_id')))
            ->when($request->filled('document_type_id'), fn ($query) => $query->where('document_type_id', $request->query('document_type_id')))
            ->when($request->filled('stage_id'), fn ($query) => $query->where('stage_id', $request->query('stage_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        $filters = $this->filterOptions();

        return view('documents.index', compact('documents', 'filters'));
    }

    public function create()
    {
        $this->authorize('create', Document::class);

        return view('documents.create', [
            'filters' => $this->filterOptions(),
        ]);
    }

    public function store(StoreDocumentRequest $request)
    {
        $this->authorize('create', Document::class);

        $document = $this->service->createDocument($request->validated(), $request->user());

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['category:id,name', 'documentType:id,name', 'stage:id,name', 'creator:id,name', 'latestVersion']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);

        return view('documents.edit', [
            'document' => $document,
            'filters' => $this->filterOptions(),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $this->authorize('update', $document);

        $this->service->updateDocument($document, $request->validated(), $request->user());

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Request $request, Document $document)
    {
        $this->authorize('delete', $document);

        $this->service->archiveDocument($document, $request->user());

        return redirect()
            ->route('documents.index')
            ->with('success', 'Dokumen berhasil diarsipkan.');
    }

    /**
     * Opsi untuk form & filter (tahun unik, kategori, jenis, tahapan, status aktif).
     */
    protected function filterOptions(): array
    {
        return [
            'years' => Document::query()->select('year')->distinct()->orderByDesc('year')->pluck('year'),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'documentTypes' => DocumentType::where('is_active', true)->orderBy('name')->get(),
            'stages' => Stage::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }
}
