<?php

namespace App\Http\Controllers;

use App\Enums\AccessLevel;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\Stage;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $service)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        [$sort, $direction, $perPage] = $this->resolveSortAndPage($request);

        $documents = Document::query()
            ->with(['category:id,name', 'documentType:id,name', 'stage:id,name'])
            ->when($search = trim((string) $request->query('q')), fn ($q) => $q->search($search))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->query('year')))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->query('category_id')))
            ->when($request->filled('document_type_id'), fn ($q) => $q->where('document_type_id', $request->query('document_type_id')))
            ->when($request->filled('stage_id'), fn ($q) => $q->where('stage_id', $request->query('stage_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->filterByAccessLevel($request->query('access_level'))
            ->filterByDateRange($request->query('date_from'), $request->query('date_to'))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        $filters = $this->filterOptions();
        $activeFilterCount = $this->countActiveFilters($request);

        return view('documents.index', compact('documents', 'filters', 'activeFilterCount'));
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

        $document->load(['category:id,name', 'documentType:id,name', 'stage:id,name', 'creator:id,name', 'versions.uploader:id,name', 'latestVersion']);

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

    public function download(Document $document)
    {
        $this->authorize('download', $document);

        $version = $document->latestVersion;

        if (! $version) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        if (! $this->service->fileService()->exists($version->file_path)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        return response()->streamDownload(
            function () use ($version) {
                echo $this->service->fileService()->content($version->file_path);
            },
            $version->original_filename ?: ($document->title . '.pdf'),
            ['Content-Type' => $version->mime_type ?? 'application/pdf'],
        );
    }

    public function downloadVersion(DocumentVersion $version)
    {
        $document = $version->document;

        $this->authorize('download', $document);

        if (! $this->service->fileService()->exists($version->file_path)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        return response()->streamDownload(
            function () use ($version) {
                echo $this->service->fileService()->content($version->file_path);
            },
            $version->original_filename ?: ($document->title . '.pdf'),
            ['Content-Type' => $version->mime_type ?? 'application/pdf'],
        );
    }

    public function preview(Document $document)
    {
        $this->authorize('preview', $document);

        $version = $document->latestVersion;

        if (! $version) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        if (! $this->service->fileService()->exists($version->file_path)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        return response()->stream(
            function () use ($version) {
                echo $this->service->fileService()->content($version->file_path);
            },
            200,
            [
                'Content-Type' => $version->mime_type ?? 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . ($version->original_filename ?? $document->title) . '"',
            ],
        );
    }

    public function storeVersion(Request $request, Document $document)
    {
        $this->authorize('manageVersions', $document);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:' . (int) config('documents.max_upload_size_kb', 20480)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->service->addVersion($document, $validated, $request->user());

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Versi baru berhasil diunggah.');
    }

    public function destroyVersion(Request $request, Document $document, DocumentVersion $version)
    {
        $this->authorize('manageVersions', $document);

        if ($version->document_id !== $document->id) {
            abort(404);
        }

        try {
            $this->service->removeVersion($document, $version, $request->user());
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('documents.show', $document)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Versi berhasil dihapus.');
    }

    public function archived(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        [$sort, $direction, $perPage] = $this->resolveSortAndPage($request);

        $documents = Document::query()
            ->onlyTrashed()
            ->with(['category:id,name', 'documentType:id,name', 'stage:id,name'])
            ->when($search = trim((string) $request->query('q')), fn ($q) => $q->search($search))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->query('year')))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->query('category_id')))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('documents.archived', compact('documents'));
    }

    public function restore(Request $request, int $id)
    {
        $document = Document::withTrashed()->findOrFail($id);

        $this->authorize('restore', $document);

        $document->restore();

        return redirect()
            ->route('documents.archived')
            ->with('success', 'Dokumen berhasil dipulihkan.');
    }

    public function recent()
    {
        $this->authorize('viewAny', Document::class);

        $documents = Document::query()
            ->with(['category:id,name', 'documentType:id,name', 'stage:id,name'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('documents.recent', compact('documents'));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Document::class);

        $query = Document::query()
            ->with(['category:id,name', 'documentType:id,name', 'stage:id,name', 'creator:id,name'])
            ->when($search = trim((string) $request->query('q')), fn ($q) => $q->search($search))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->query('year')))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->query('category_id')))
            ->when($request->filled('document_type_id'), fn ($q) => $q->where('document_type_id', $request->query('document_type_id')))
            ->when($request->filled('stage_id'), fn ($q) => $q->where('stage_id', $request->query('stage_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->filterByAccessLevel($request->query('access_level'))
            ->filterByDateRange($request->query('date_from'), $request->query('date_to'))
            ->orderBy('created_at', 'desc');

        $filename = 'dokumen_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Judul', 'Nomor', 'Jenis', 'Kategori', 'Tahapan', 'Tahun', 'Status', 'Akses', 'Tanggal', 'Dibuat Oleh']);

            $query->chunk(200, function ($documents) use ($handle) {
                foreach ($documents as $doc) {
                    fputcsv($handle, [
                        $doc->title,
                        $doc->document_number ?? '',
                        $doc->documentType?->name ?? '',
                        $doc->category?->name ?? '',
                        $doc->stage?->name ?? '',
                        $doc->year,
                        $doc->status?->label() ?? $doc->status,
                        $doc->access_level?->label() ?? $doc->access_level,
                        $doc->document_date?->format('d/m/Y') ?? '',
                        $doc->creator?->name ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    protected function resolveSortAndPage(Request $request): array
    {
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = (int) $request->query('per_page', 15);

        $allowedSorts = ['created_at', 'document_date', 'year', 'title'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';
        $perPage = in_array($perPage, [15, 25, 50, 100], true) ? $perPage : 15;

        return [$sort, $direction, $perPage];
    }

    protected function countActiveFilters(Request $request): int
    {
        $count = 0;
        foreach (['q', 'year', 'category_id', 'document_type_id', 'stage_id', 'status', 'access_level', 'date_from', 'date_to'] as $param) {
            if ($request->filled($param)) {
                $count++;
            }
        }

        return $count;
    }

    protected function filterOptions(): array
    {
        return [
            'years' => Document::query()->select('year')->distinct()->orderByDesc('year')->pluck('year'),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'documentTypes' => DocumentType::where('is_active', true)->orderBy('name')->get(),
            'stages' => Stage::where('is_active', true)->orderBy('election_type')->orderBy('sort_order')->orderBy('name')->get(),
            'accessLevels' => AccessLevel::cases(),
        ];
    }
}
