<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthCheck extends Command
{
    protected $signature = 'app:health {--json : Output as JSON}';

    protected $description = 'Periksa status kesehatan aplikasi (DB, cache, storage).';

    public function handle(): int
    {
        $checks = [];

        $checks['Database'] = $this->checkDatabase();
        $checks['Cache'] = $this->checkCache();
        $checks['Storage'] = $this->checkStorage();

        if ($this->option('json')) {
            $this->line(json_encode($checks, JSON_PRETTY_PRINT));
        } else {
            $this->table(['Komponen', 'Status', 'Detail'], array_map(
                fn ($c) => [$c['name'], $c['ok'] ? 'OK' : 'GAGAL', $c['detail']],
                $checks
            ));
        }

        $failed = collect($checks)->contains(fn ($c) => ! $c['ok']);

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    protected function checkDatabase(): array
    {
        $name = 'Database';
        try {
            DB::select('select 1');

            return ['name' => $name, 'ok' => true, 'detail' => 'Koneksi berhasil'];
        } catch (\Throwable $e) {
            return ['name' => $name, 'ok' => false, 'detail' => $e->getMessage()];
        }
    }

    protected function checkCache(): array
    {
        $name = 'Cache';
        try {
            Cache::put('health_check', 'ok', 10);
            $val = Cache::get('health_check');
            Cache::forget('health_check');

            return ['name' => $name, 'ok' => $val === 'ok', 'detail' => $val === 'ok' ? 'Baca/tulis berhasil' : 'Nilai cache tidak cocok'];
        } catch (\Throwable $e) {
            return ['name' => $name, 'ok' => false, 'detail' => $e->getMessage()];
        }
    }

    protected function checkStorage(): array
    {
        $name = 'Storage';
        try {
            $dir = storage_path('app/private');
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $testFile = $dir.'/.health_check';
            $result = @file_put_contents($testFile, 'ok') !== false;
            if ($result) {
                @unlink($testFile);
            }

            return ['name' => $name, 'ok' => $result, 'detail' => $result ? 'Tulis/hapus berhasil' : 'Gagal menulis'];
        } catch (\Throwable $e) {
            return ['name' => $name, 'ok' => false, 'detail' => $e->getMessage()];
        }
    }
}
