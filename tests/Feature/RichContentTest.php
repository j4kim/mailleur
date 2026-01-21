<?php

namespace Tests\Feature;

use App\Models\Recipient;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tiptap\Editor;

use function App\Tools\renderRichText;
use function App\Tools\replaceLinks;
use function App\Tools\replaceMergeTags;

class RichContentTest extends TestCase
{
    use RefreshDatabase;

    private array $doc = [
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

    public function test_html_renderer(): void
    {
        $rendered = renderRichText($this->doc);

        $this->assertEquals($rendered, "<p>Salut <span></span></p>");
    }

    public function test_convert_twice_to_remove_merge_tags(): void
    {
        $rendered = renderRichText($this->doc);

        $array = (new Editor)->setContent($rendered)->getDocument();

        $this->assertEquals($array, [
            "type" => "doc",
            "content" => [
                [
                    "type" => "paragraph",
                    "content" => [
                        ["type" => "text", "text" => "Salut "],
                    ],
                ],
            ],
        ]);
    }

    public function test_replace_merge_tags(): void
    {
        $doc = replaceMergeTags($this->doc, ['name' => 'Sandwich']);
        $this->assertEquals($doc, [
            "type" => "doc",
            "content" => [
                [
                    "type" => "paragraph",
                    "attrs" => ["textAlign" => "start"],
                    "content" => [
                        ["type" => "text", "text" => "Salut Sandwich"],
                    ],
                ],
            ],
        ]);
    }

    public function test_replace_merge_tags_and_render(): void
    {
        $doc = replaceMergeTags($this->doc, ['name' => 'Sandwich']);
        $rendered = RichContentRenderer::make($doc)->toHtml();
        $this->assertEquals($rendered, "<p>Salut Sandwich</p>");
    }

    public function test_replace_merge_tags_with_empty_merge_tag(): void
    {
        $doc = replaceMergeTags($this->doc, ['name' => '']);
        $this->assertEquals($doc, [
            "type" => "doc",
            "content" => [
                [
                    "type" => "paragraph",
                    "attrs" => ["textAlign" => "start"],
                    "content" => [
                        ["type" => "text", "text" => "Salut "],
                    ],
                ],
            ],
        ]);
    }

    public function test_replace_merge_tags_with_empty_merge_tag_and_render(): void
    {
        $doc = replaceMergeTags($this->doc, ['name' => '']);
        $rendered = RichContentRenderer::make($doc)->toHtml();
        $this->assertEquals($rendered, "<p>Salut </p>");
    }

    public function test_replace_merge_tags_with_no_merge_tags_and_render(): void
    {
        $doc = replaceMergeTags($this->doc, []);
        $rendered = RichContentRenderer::make($doc)->toHtml();
        $this->assertEquals($rendered, "<p>Salut </p>");
    }

    public function test_replace_links(): void
    {
        $doc = [
            "type" => "doc",
            "content" => [
                [
                    "type" => "paragraph",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Doc Laravel",
                            "marks" => [
                                [
                                    "type" => "link",
                                    "attrs" => [
                                        "href" => "https://laravel.com/docs/",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];
        $this->seed();
        $recipient = Recipient::first();
        $updated = replaceLinks($doc, $recipient);
        $rendered = RichContentRenderer::make($updated)->toHtml();
        $href = route('link-redirect', $recipient->links()->first()->token);
        $this->assertEquals($rendered, '<p><a target="_blank" rel="noopener noreferrer nofollow" href="' . $href . '">Doc Laravel</a></p>');
    }
}
