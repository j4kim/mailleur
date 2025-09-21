<?php

namespace App\Models;

use App\Enums\RecipientStatus;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipient extends Model
{
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
            ->mergeTags($this->getMergeTags())
            ->toHtml();
    }

    public function generateAndSave()
    {
        $this->mail_body = $this->generateMailBody();
        $this->status = RecipientStatus::Customized;
        $this->save();
    }
}
