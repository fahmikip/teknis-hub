<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DocumentVersionUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document,
        public DocumentVersion $version,
        public string $actorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'version_number' => $this->version->version_number,
            'actor_name' => $this->actorName,
            'action' => 'version_uploaded',
            'message' => sprintf('%s mengunggah versi v%d untuk dokumen "%s"', $this->actorName, $this->version->version_number, $this->document->title),
        ];
    }
}
