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

    public function getTeam(): Team
    {
        return Filament::getTenant();
    }

    public function getAddress(string $key): ?Address
    {
        $addr = $this->campaign->envelope[$key];
        if (!empty($addr['address'])) {
            return new Address(...$addr);
        }
        return null;
    }

    public function getFrom(): Address|string
    {
        return $this->getAddress("from") ?? $this->getTeam()->smtp_config['username'];
    }

    public function getReplyTo(): ?array
    {
        $address = $this->getAddress("replyTo");
        return $address ? [$address] : null;
    }

    /**
     * @return array<Address>
     */
    public function getAddresses(string $key): array
    {
        $cc = collect($this->campaign->envelope[$key]);
        return $cc->map(fn($a) => new Address(...$a))->toArray();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
            from: $this->getFrom(),
            replyTo: $this->getReplyTo(),
            cc: $this->getAddresses("cc"),
            bcc: $this->getAddresses("bcc"),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->recipient->mail_body
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
