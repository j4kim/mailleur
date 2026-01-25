<?php

namespace App\Mail;

use App\Enums\RecipientStatus;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ScheduledReport extends Mailable
{
    use Queueable, SerializesModels;

    public string $campaignUrl;
    public Collection $successRecipients;
    public Collection $failedRecipients;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Campaign $campaign,
        public Collection $recipients,
    ) {
        $this->campaignUrl = CampaignResource::getUrl('view', [
            'record' => $campaign,
            'tenant' => $campaign->team
        ]);
        $this->successRecipients = $recipients->where('status', RecipientStatus::Sent);
        $this->failedRecipients = $recipients->where('status', RecipientStatus::Failed);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Scheduled campaign report - {$this->campaign->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.campaigns.scheduled-report',
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
