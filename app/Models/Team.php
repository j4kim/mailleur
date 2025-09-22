<?php

namespace App\Models;

use App\Mail\TeamInvitation;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;

use function App\Tools\emailToName;

class Team extends Model
{
    protected function casts(): array
    {
        return [
            'smtp_config' => 'encrypted:array',
            'defaults' => 'array',
        ];
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_admin');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function inviteUser(array $data)
    {
        $email = strtolower($data['email']);
        $is_admin = $data['is_admin'];

        $user = User::firstOrNew(['email' => $email]);

        if ($user->exists) {
            if ($user->canAccessTenant($this)) {
                throw new Exception("User already in team");
            }
        } else {
            $user->name = emailToName($email);
            $user->password = "to be reset";
            $user->save();
        }

        Mail::to($user)->send(new TeamInvitation($this));

        $this->members()->attach($user, ['is_admin' => $is_admin]);
    }

    public function configureMailer()
    {
        $smtpc = $this->smtp_config;

        foreach (['host', 'port', 'password', 'username'] as $key) {
            if (!$smtpc[$key]) {
                throw new Exception("$key missing in Team SMTP config");
            }
        }

        config([
            'mail.mailers.smtp' => array_merge(
                config('mail.mailers.smtp'),
                $smtpc
            )
        ]);
    }
}
