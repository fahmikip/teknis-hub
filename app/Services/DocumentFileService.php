<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DocumentFileService
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('documents.disk', 'local');
    }

    /**
     * Generate relative path dokumen privat: documents/{year}/{uuid}.pdf
     */
    public function generatePath(UploadedFile $file, int $year): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'pdf';
        $filename = Str::uuid()->toString() . '.' . strtolower($extension);

        return sprintf('documents/%d/%s', $year, $filename);
    }

    /**
     * Simpan file ke disk privat.
     *
     * @return string relative path yang tersimpan
     */
    public function store(UploadedFile $file, int $year): string
    {
        $path = $this->generatePath($file, $year);
        $options = ['disk' => $this->disk, 'visibility' => 'private'];

        if (! Storage::disk($this->disk)->putFileAs('documents/' . $year, $file, basename($path), $options)) {
            throw new \RuntimeException('Gagal menyimpan file dokumen.');
        }

        return $path;
    }

    /**
     * Hapus file (untuk rollback / cleanup), abaikan bila tidak ada.
     */
    public function delete(string $path): bool
    {
        if (! $path || ! Storage::disk($this->disk)->exists($path)) {
            return true;
        }

        return Storage::disk($this->disk)->delete($path);
    }

    public function disk(): string
    {
        return $this->disk;
    }
}
