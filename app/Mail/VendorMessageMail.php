<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\VendorAdvertisement;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class VendorMessageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VendorAdvertisement $advertisement;
    public string $adminMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(VendorAdvertisement $advertisement, string $adminMessage)
    {
        $this->advertisement = $advertisement;
        $this->adminMessage = $adminMessage;

        // Ensure vendor relationship is loaded
        if (!$advertisement->relationLoaded('vendor')) {
            $this->advertisement->load('vendor');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Message Regarding Your Advertisement: ' . $this->advertisement->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-message',
            with: [
                'advertisement' => $this->advertisement,
                'adminMessage' => $this->adminMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
