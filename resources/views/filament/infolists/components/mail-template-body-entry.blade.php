@php
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div {{ $getExtraAttributeBag() }}>
        {{ RichContentRenderer::make($record->body)->mergeTags([
            'name' => 'Joe',
        ]) }}
    </div>
</x-dynamic-component>
