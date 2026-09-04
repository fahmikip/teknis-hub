<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--keep=7 : Number of backups to keep}';

    protected $description = 'Create a database backup and store it locally';

    public function handle(): int
    {
        $date = now()->format('Y-m-d_His');
        $backupDir = storage_path('app/backups/database');
        $filename = "backup_{$date}.sql";
        $filepath = "{$backupDir}/{$filename}";

        File::ensureDirectoryExists($backupDir);

        $dbConfig = config('database.connections.' . config('database.default'));

        $this->info("Starting database backup...");

        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = $dbConfig['database'] ?? config('database.default');
            $tableKey = "Tables_in_{$dbName}";

            $handle = fopen($filepath, 'w');

            fwrite($handle, "-- TeknisHub Database Backup\n");
            fwrite($handle, "-- Date: {$date}\n");
            fwrite($handle, "-- Database: {$dbName}\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $table) {
                $tableName = $table->{$tableKey};

                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createTable)) {
                    fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                    fwrite($handle, $createTable[0]->{'Create Table'} . ";\n\n");
                }

                $rows = DB::table($tableName)->get();
                if ($rows->isEmpty()) {
                    continue;
                }

                $columns = array_keys((array) $rows->first());
                $columnList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));

                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return DB::getPdo()->quote($value);
                    }, (array) $row);

                    fwrite($handle, "INSERT INTO `{$tableName}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n");
                }
                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            $size = number_format(File::size($filepath) / 1024, 2);
            $this->info("Backup created: {$filename} ({$size} KB)");

            $this->cleanupOldBackups($backupDir, $this->option('keep'));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Backup failed: {$e->getMessage()}");
            if (File::exists($filepath)) {
                File::delete($filepath);
            }
            return self::FAILURE;
        }
    }

    protected function cleanupOldBackups(string $directory, int $keep): void
    {
        $files = collect(File::files($directory))
            ->filter(fn($f) => $f->getExtension() === 'sql')
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
