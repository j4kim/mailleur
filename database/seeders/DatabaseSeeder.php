<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $campaign = Campaign::create([
            'subject' => "Hello",
            'template' => [
                "type" => "doc",
                "content" => [
                    [
                        "type" => "paragraph",
                        "content" => [
                            [ "type" => "text", "text" => "Salut ", ],
                            [ "type" => "mergeTag", "attrs" => ["id" => "name"], ],
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
