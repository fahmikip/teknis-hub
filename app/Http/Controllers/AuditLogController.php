<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AuditLog::class);

        $logs = AuditLog::query()
            ->with('user:id,name')
            ->with('auditable')
            ->when($search = trim((string) $request->query('q')), function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when($request->filled('action') && $request->query('action') !== '', function ($query) use ($request) {
                $query->where('action', $request->query('action'));
            })
            ->when($request->filled('user_id') && $request->query('user_id') !== '', function ($query) use ($request) {
                $query->where('user_id', $request->query('user_id'));
            })
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->query('date_to')))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('audit-logs.index', [
            'logs' => $logs,
            'actions' => AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
            'users' => \App\Models\User::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }
}