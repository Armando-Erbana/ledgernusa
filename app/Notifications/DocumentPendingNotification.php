<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentPendingNotification extends Notification
{
    use Queueable;
    public $document;
    public $type;
    public $url;

    public function __construct($document, $type, $url)
    {
        $this->document = $document;
        $this->type = $type;
        $this->url = $url;
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        $docNumber = $this->document->po_number ?? $this->document->so_number ?? 'DOC';
        return [
            'message' => 'Dokumen ' . $docNumber . ' menunggu persetujuan Anda.',
            'url' => $this->url,
        ];
    }
}