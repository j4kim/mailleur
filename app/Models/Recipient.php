<?php

namespace App\Models;

use App\Enums\RecipientStatus;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\LoggedLink;
use App\Mail\CampaignMail;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class Recipient extends Model
{

    protected $touches = ['campaign'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => RecipientStatus::class,
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtolower($value),
        );
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getMergeTags(): array
    {
        $data = $this->data ?? [];
        return ['email' => $this->email, ...$data];
    }

    public function generateMailBody(): ?string
    {
        $template = $this->campaign->template;
        if (!$template) return null;
        return RichContentRenderer::make($template)
            ->customBlocks([
                LoggedLink::class => [
                    'campaign' => $this->campaign,
                    'recipient' => $this,
                ],
            ])
            ->mergeTags($this->getMergeTags())
            ->toHtml();
    }

    public function generateAndSave()
    {
        $this->mail_body = $this->generateMailBody();
        $this->status = RecipientStatus::Customized;
        $this->save();
    }

    public static function configureSmtp()
    {
        /** @var Team $team */
        $team = Filament::getTenant();
        $team->configureMailer();
    }

    public function send()
    {
        if (!$this->mail_body) {
            $this->mail_body = $this->generateMailBody();
        }
        try {
            Mail::to($this->email)->send(new CampaignMail($this));
            $this->status = RecipientStatus::Sent;
            $this->sent_at = now();
            $this->save();
        } catch (Exception $e) {
            $this->status = RecipientStatus::Failed;
            $this->failed_at = now();
            $this->save();
            throw $e;
        }
    }

    public function sendOne()
    {
        if (!$this->mail_body) {
            throw new Exception("No mail body for recipient $this->email");
        }
        self::configureSmtp();
        $this->send();
    }

    public static function sendMany(Collection $recipients): array
    {
        $successCount = 0;
        $errorCount = 0;
        $filtered = $recipients->where('status', '!==', RecipientStatus::Sent);
        self::configureSmtp();
        foreach ($filtered as $recipient) {
            try {
                $recipient->send();
                $successCount++;
            } catch (Exception $e) {
                $errorCount++;
            }
        }
        return [$successCount, $errorCount];
    }
}
