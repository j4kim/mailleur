@php
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    $richTextJson = $getState();
@endphp

@if($richTextJson)
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class='prose bg-white dark:bg-gray-900 dark:prose-invert border border-current/10 py-4 px-6 rounded-xl' {{ $getExtraAttributeBag() }}>
        {{ RichContentRenderer::make($richTextJson)->mergeTags([
            'name' => 'Joe',
        ]) }}
    </div>
</x-dynamic-component>
@endif