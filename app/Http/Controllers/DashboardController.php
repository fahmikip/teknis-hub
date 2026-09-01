<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $currentYear = (int) date('Y');

        $stats = [
            'totalDocuments' => Document::count(),
            'totalArchived' => Document::onlyTrashed()->count(),
            'totalVersions' => Document::query()->withCount('versions')->get()->sum('versions_count'),
            'currentYearDocuments' => Document::where('year', $currentYear)->count(),
            'activeDocuments' => Document::where('status', DocumentStatus::Active->value)->count(),
            'newThisMonth' => Document::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'documentTypes' => DocumentType::count(),
            'favoriteCount' => Favorite::where('user_id', $request->user()->id)->count(),
        ];

        $statusStats = Document::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->mapWithKeys(fn ($total, $status) => [
                DocumentStatus::tryFrom($status)?->label() ?? $status => $total,
            ]);

        $recentDocuments = Document::query()
            ->with(['category:id,name', 'documentType:id,name', 'stage:id,name'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $recentActivity = AuditLog::query()
            ->with('user:id,name')
            ->latest()
            ->limit(8)
            ->get();

        $favoriteDocuments = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->with(['document:id,title,year,status', 'document.category:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'statusStats',
            'currentYear',
            'recentDocuments',
            'recentActivity',
            'favoriteDocuments'
        ));
    }
}