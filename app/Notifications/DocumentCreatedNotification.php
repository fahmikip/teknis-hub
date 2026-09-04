<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document,
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
            'actor_name' => $this->actorName,
            'action' => 'created',
            'message' => sprintf('%s membuat dokumen baru "%s"', $this->actorName, $this->document->title),
        ];
    }
}
