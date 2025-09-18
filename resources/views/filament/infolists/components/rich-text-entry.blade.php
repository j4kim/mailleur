@php
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    $template = $getState();
    $rendered = "";
    if ($template) {
        $recipient = $record->recipients()->latest('updated_at')->first();
        if ($recipient) {
            $mergeTags = $recipient->data;
        } else {
            $mergeTags = collect($record->columns)
                ->mapWithKeys(fn($c) => [$c => "[$c]"])
                ->toArray();
        }
        $rendered = RichContentRenderer::make($template)->mergeTags($mergeTags);
    }
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class='prose bg-white dark:bg-gray-900 dark:prose-invert border border-current/10 py-4 px-6 rounded-xl' {{ $getExtraAttributeBag() }}>
        {{ $rendered }}
    </div>
</x-dynamic-component>