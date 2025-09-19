<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Info;
use League\Csv\Reader;

class Campaign extends Model
{
    protected function casts(): array
    {
        return [
            'template' => 'array',
            'columns' => 'array',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
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
            $this->columns = collect($this->columns)->concat($header)->unique();
            $this->save();
            foreach ($csv as $index => $row) {
                $row = collect($row)->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();
                $email = strtolower($row['email']);
                $validator = Validator::make($row, [
                    'email' => 'required|email'
                ]);
                if ($validator->fails()) {
                    $errors = collect($validator->errors())->flatten()->join(" ");
                    abort(422, "Row $index ($email) has invalid data. " . $errors);
                }
                $recipient = $this->recipients()->firstOrNew(['email' => $email]);
                if ($recipient->exists) {
                    $recipient->data = array_merge($recipient->data, $row);
                } else {
                    $recipient->data = $row;
                }
                $recipient->save();
            }
        });
    }
}
