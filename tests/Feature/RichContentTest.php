<?php

namespace Tests\Feature;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RichContentTest extends TestCase
{
    public function test_html_renderer(): void
    {
        $document = [
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
        ];

        $rendered = RichContentRenderer::make($document)->toHtml();

        $this->assertEquals($rendered, "<p>Salut <span></span></p>");
    }
}
