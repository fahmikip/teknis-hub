<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->with(['document:id,title,created_at,status', 'document.category:id,name', 'document.documentType:id,name'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('view', $document);

        $existing = Favorite::where('user_id', $request->user()->id)
            ->where('document_id', $document->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Dokumen dihapus dari favorit.';
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'document_id' => $document->id,
            ]);
            $message = 'Dokumen ditambahkan ke favorit.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function destroy(Request $request, Favorite $favorite): RedirectResponse
    {
        abort_unless($favorite->user_id === $request->user()->id, 403);

        $favorite->delete();

        return redirect()
            ->route('favorites.index')
            ->with('success', 'Dokumen dihapus dari favorit.');
    }
}