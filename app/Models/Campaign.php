<?php

namespace App\Models;

use App\Enums\EventLogType;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Info;
use League\Csv\Reader;

use function App\Tools\prose;

class Campaign extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Campaign $campaign) {
            $campaign->template = @$campaign->team->defaults['template'];
            $campaign->envelope = @$campaign->team->defaults['envelope'];
        });

        static::created(function (Campaign $campaign) {
            $campaign->logEvent(EventLogType::CampaignCreated);
        });
    }

    protected function casts(): array
    {
        return [
            'template' => 'array',
            'columns' => 'array',
            'envelope' => 'array',
            'enable_logged_links' => 'boolean',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class)->chaperone();
    }

    public function eventLogs(): HasMany
    {
        return $this->hasMany(EventLog::class);
    }

    public function logEvent(EventLogType $type, ?array $meta = null): EventLog
    {
        return $this->eventLogs()->create([
            'type' => $type,
            'user_id' => Auth::id(),
            'meta' => $meta,
        ]);
    }

    public function getMergeTags(): array
    {
        $columns = $this->columns ?? [];
        return ['email', ...$columns];
    }

    public function importCsv(string $filename)
    {
        $path = Storage::path($filename);
        $csv = Reader::createFromPath($path);
        $csv->setHeaderOffset(0);
        $delimiter = collect(Info::getDelimiterStats($csv, [",", ";"]))->sortDesc()->keys()->first();
        $csv->setDelimiter($delimiter);
        DB::transaction(function () use ($csv) {
            $header = collect($csv->getHeader())->map("strtolower");
            if ($header->doesntContain('email')) {
                abort(422, 'The file must contain an "email" header');
            }
            $this->columns = collect($this->columns)->concat($header)->unique()->reject('email')->values();
            $this->save();
            foreach ($csv as $index => $row) {
                $row = collect($row)->mapWithKeys(fn($v, $k) => [strtolower($k) => trim($v)]);
                $email = strtolower($row['email']);
                $validator = Validator::make($row->toArray(), [
                    'email' => 'required|email'
                ]);
                if ($validator->fails()) {
                    $errors = collect($validator->errors())->flatten()->join(" ");
                    abort(422, "Row $index ($email) has invalid data. " . $errors);
                }
                $recipient = $this->recipients()->firstOrNew(['email' => $email]);
                $data = $row->except('email');
                if ($recipient->exists) {
                    $recipient->data = array_merge($recipient->data, $data->toArray());
                } else {
                    $recipient->data = $data;
                }
                $recipient->save();
            }
        });
    }

    public function renderTemplate()
    {
        if (!$this->template) {
            return "";
        }
        $mergeTags = collect($this->getMergeTags())
            ->mapWithKeys(fn($c) => [$c => "{{ $c }}"])
            ->toArray();
        $rendered = RichContentRenderer::make($this->template)
            ->mergeTags($mergeTags)
            ->toHtml();
        return prose($rendered);
    }

    public function getAddress(string $key): ?Address
    {
        $addr = @$this->envelope[$key];
        if (!empty($addr['address'])) {
            return new Address(...$addr);
        }
        return null;
    }

    public function getFrom(): Address
    {
        return $this->getAddress("from") ?? new Address($this->team->smtp_config['username']);
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
        $cc = collect(@$this->envelope[$key]);
        return $cc->map(fn($a) => new Address(...$a))->toArray();
    }
}
