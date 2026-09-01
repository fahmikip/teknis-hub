<?php

namespace App\Http\Controllers;

use App\Enums\ElectionType;
use App\Http\Requests\StoreStageRequest;
use App\Http\Requests\UpdateStageRequest;
use App\Models\Stage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StageController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Stage::class);

        $stages = Stage::query()
            ->withCount('documents')
            ->when($search = trim((string) $request->query('q')), function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->has('election_type') && $request->query('election_type') !== '', function ($query) use ($request) {
                $query->where('election_type', $request->query('election_type'));
            })
            ->when($request->has('status') && $request->query('status') !== '', fn ($query) => $query->where('is_active', $request->boolean('status')))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('stages.index', [
            'stages' => $stages,
            'electionTypes' => ElectionType::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Stage::class);

        return view('stages.create', [
            'electionTypes' => ElectionType::cases(),
            'stages' => Stage::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreStageRequest $request): RedirectResponse
    {
        $this->authorize('create', Stage::class);

        $stage = Stage::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'election_type' => $request->election_type,
            'description' => $request->description,
            'parent_id' => $request->filled('parent_id') ? $request->parent_id : null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('stages.index')
            ->with('success', 'Tahapan berhasil ditambahkan.');
    }

    public function edit(Stage $stage): View
    {
        $this->authorize('update', $stage);

        return view('stages.edit', [
            'stage' => $stage,
            'electionTypes' => ElectionType::cases(),
            'stages' => Stage::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->reject(fn ($item) => $item->id === $stage->id),
        ]);
    }

    public function update(UpdateStageRequest $request, Stage $stage): RedirectResponse
    {
        $this->authorize('update', $stage);

        $stage->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'election_type' => $request->election_type,
            'description' => $request->description,
            'parent_id' => $request->filled('parent_id') ? $request->parent_id : null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('stages.index')
            ->with('success', 'Tahapan berhasil diperbarui.');
    }

    public function destroy(Stage $stage): RedirectResponse
    {
        $this->authorize('delete', $stage);

        if ($stage->documents()->exists()) {
            return redirect()
                ->route('stages.index')
                ->with('error', 'Tahapan tidak dapat dihapus karena masih digunakan oleh dokumen.');
        }

        $stage->delete();

        return redirect()
            ->route('stages.index')
            ->with('success', 'Tahapan berhasil dihapus.');
    }
}