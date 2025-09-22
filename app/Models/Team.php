<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function App\Tools\emailToName;

class Team extends Model
{
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

        $this->members()->attach($user, ['is_admin' => $is_admin]);
    }
}
