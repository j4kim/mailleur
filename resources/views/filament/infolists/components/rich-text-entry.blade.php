@php
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    $richTextJson = $getState();
@endphp

@if($richTextJson)
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class='prose dark:prose-invert border border-current/30 p-4 rounded-lg' {{ $getExtraAttributeBag() }}>
        {{ RichContentRenderer::make($richTextJson)->mergeTags([
            'name' => 'Joe',
        ]) }}
    </div>
</x-dynamic-component>
@endif