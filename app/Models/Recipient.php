<?php

namespace App\Models;

use App\Enums\EventLogType;
use App\Enums\RecipientStatus;
use App\Mail\CampaignMail;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use function App\Tools\replaceMergeTags;

class Recipient extends Model
{
    protected static function booted(): void
    {
        static::created(function (Recipient $recipient) {
            $recipient->logEvent(EventLogType::RecipientCreated);
        });

        static::updated(function (Recipient $recipient) {
            if ($recipient->wasChanged('status')) {
                $recipient->logEvent(EventLogType::StatusChanged, [
                    'old' => $recipient->getOriginal('status'),
                    'new' => $recipient->status,
                ]);
            }
        });
    }

    protected $touches = ['campaign'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'mail_body' => 'array',
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

    public function eventLogs(): HasMany
    {
        return $this->hasMany(EventLog::class);
    }

    public function logEvent(EventLogType $type, ?array $meta = null): EventLog
    {
        return $this->eventLogs()->create([
            'type' => $type,
            'campaign_id' => $this->campaign_id,
            'user_id' => Auth::id(),
            'meta' => $meta,
        ]);
    }

    public function getMergeTags(): array
    {
        $data = $this->data ?? [];
        return ['email' => $this->email, ...$data];
    }

    public function generateMailBody(): ?array
    {
        $template = $this->campaign->template;
        if (!$template) return null;
        return replaceMergeTags($template, $this->getMergeTags());
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
        $this->rendered_mail_body = RichContentRenderer::make($this->mail_body)->toHtml();
        try {
            $mail = new CampaignMail($this);
            Mail::to($this->email)->send($mail);
            $this->status = RecipientStatus::Sent;
            $this->sent_at = now();
            $this->save();
            $this->logEvent(EventLogType::MailSent, [
                'content' => $mail->content()->htmlString,
                'envelope' => $mail->envelope(),
            ]);
        } catch (Exception $e) {
            $this->status = RecipientStatus::Failed;
            $this->failed_at = now();
            $this->save();
            $this->logEvent(EventLogType::SendingFailed, [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
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
