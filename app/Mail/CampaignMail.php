<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Recipient;
use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public Recipient $recipient;
    public Campaign $campaign;

    /**
     * Create a new message instance.
     */
    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
        $this->campaign = $recipient->campaign;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
            from: $this->campaign->getFrom(),
            replyTo: $this->campaign->getReplyTo(),
            cc: $this->campaign->getAddresses("cc"),
            bcc: $this->campaign->getAddresses("bcc"),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.campaign'
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
