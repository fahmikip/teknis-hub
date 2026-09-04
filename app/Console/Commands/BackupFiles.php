<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupFiles extends Command
{
    protected $signature = 'backup:files
                            {--keep=7 : Number of file backups to keep}';

    protected $description = 'Create a compressed backup of document files';

    public function handle(): int
    {
        $date = now()->format('Y-m-d_His');
        $backupDir = storage_path('app/backups/files');
        $sourceDir = storage_path('app/private/documents');
        $filename = "files_{$date}.zip";
        $filepath = "{$backupDir}/{$filename}";

        File::ensureDirectoryExists($backupDir);

        if (!File::isDirectory($sourceDir)) {
            $this->warn('No document files directory found. Skipping file backup.');
            return self::SUCCESS;
        }

        $this->info("Starting file backup...");

        try {
            $zip = new \ZipArchive();
            if ($zip->open($filepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Could not create zip archive.');
            }

            $this->addFilesToZip($zip, $sourceDir, $sourceDir);
            $zip->close();

            $size = number_format(File::size($filepath) / 1024 / 1024, 2);
            $this->info("File backup created: {$filename} ({$size} MB)");

            $this->cleanupOldBackups($backupDir, $this->option('keep'));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("File backup failed: {$e->getMessage()}");
            if (File::exists($filepath)) {
                File::delete($filepath);
            }
            return self::FAILURE;
        }
    }

    protected function addFilesToZip(\ZipArchive $zip, string $sourceDir, string $baseDir): void
    {
        $files = File::allFiles($sourceDir);
        foreach ($files as $file) {
            $relativePath = ltrim(str_replace($baseDir, '', $file->getPathname()), DIRECTORY_SEPARATOR);
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    protected function cleanupOldBackups(string $directory, int $keep): void
    {
        $files = collect(File::files($directory))
            ->filter(fn($f) => $f->getExtension() === 'zip')
            ->sortBy(fn($f) => $f->getFilename())
            ->values();

        if ($files->count() > $keep) {
            $toDelete = $files->slice(0, $files->count() - $keep);
            foreach ($toDelete as $file) {
                File::delete($file->getPathname());
                $this->line("  Cleaned up: {$file->getFilename()}");
            }
        }
    }
}
