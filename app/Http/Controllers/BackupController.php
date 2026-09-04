<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $this->authorize('manageSettings', \App\Models\Setting::class);

        $dbBackups = $this->getBackups('database', 'sql');
        $fileBackups = $this->getBackups('files', 'zip');

        return view('backups.index', compact('dbBackups', 'fileBackups'));
    }

    public function backupDatabase(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('manageSettings', \App\Models\Setting::class);

        $exitCode = Artisan::call('backup:database', ['--keep' => 7]);
        $output = Artisan::output();

        if ($exitCode === 0) {
            return back()->with('success', 'Backup database berhasil dibuat.');
        }

        return back()->with('error', 'Backup database gagal: ' . $output);
    }

    public function backupFiles(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('manageSettings', \App\Models\Setting::class);

        $exitCode = Artisan::call('backup:files', ['--keep' => 7]);
        $output = Artisan::output();

        if ($exitCode === 0) {
            return back()->with('success', 'Backup file berhasil dibuat.');
        }

        return back()->with('error', 'Backup file gagal: ' . $output);
    }

    public function destroy(Request $request, string $filename): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('manageSettings', \App\Models\Setting::class);

        $filename = basename($filename);

        $dbPath = storage_path("app/backups/database/{$filename}");
        $filePath = storage_path("app/backups/files/{$filename}");

        if (File::exists($dbPath)) {
            File::delete($dbPath);
        } elseif (File::exists($filePath)) {
            File::delete($filePath);
        } else {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        return back()->with('success', 'Backup berhasil dihapus.');
    }

    protected function getBackups(string $subdirectory, string $extension): \Illuminate\Support\Collection
    {
        $dir = storage_path("app/backups/{$subdirectory}");

        if (!File::isDirectory($dir)) {
            return collect();
        }

        return collect(File::files($dir))
            ->filter(fn ($f) => $f->getExtension() === $extension)
            ->map(fn ($f) => [
                'name' => $f->getFilename(),
                'size' => $f->getSize(),
                'size_formatted' => $this->formatSize($f->getSize()),
                'date' => $f->getMTime(),
                'date_formatted' => date('d M Y H:i', $f->getMTime()),
            ])
            ->sortByDesc('date')
            ->values();
    }

    protected function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
