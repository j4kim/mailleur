@php
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    $template = $getState();
    $rendered = '';
    if ($template) {
        $mergeTags = collect($record->columns)
            ->push('email')
            ->mapWithKeys(fn($c) => [$c => '{{ $c }}'])
            ->toArray();
        $rendered = RichContentRenderer::make($template)->mergeTags($mergeTags);
    }
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class='prose fi-section text-gray-950 dark:text-white py-4 px-6' {{ $getExtraAttributeBag() }}>
        {{ $rendered }}
    </div>
</x-dynamic-component>
