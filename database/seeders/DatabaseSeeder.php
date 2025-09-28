<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $team = Team::create([
            'name' => 'Test Team',
            'smtp_config' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'password' => config('mail.mailers.smtp.password'),
                'username' => config('mail.mailers.smtp.username'),
            ],
        ]);

        $team->members()->attach($user, ['is_admin' => true]);

        $campaign = $team->campaigns()->create([
            'subject' => "Hello",
            'template' => [
                "type" => "doc",
                "content" => [
                    [
                        "type" => "paragraph",
                        "content" => [
                            ["type" => "text", "text" => "Salut "],
                            ["type" => "mergeTag", "attrs" => ["id" => "name"]],
                        ],
                    ],
                ],
            ],
            'columns' => ['name'],
        ]);

        $campaign->recipients()->create([
            'email' => 'jivkim@gmail.com',
            'data' => ['name' => 'Joaquim'],
        ]);
    }
}
