@php
    use App\Enums\EventLogType;
    use App\Enums\RecipientStatus;
    $meta = $eventLog->meta;
@endphp

<div>
    @if ($eventLog->type === EventLogType::StatusChanged)
        @php
            $old = RecipientStatus::from($meta['old']);
            $new = RecipientStatus::from($meta['new']);
        @endphp
        From
        <x-filament::badge :color="$old->getColor()" size="sm">
            {{ $old->getLabel() }}
        </x-filament::badge>
        to
        <x-filament::badge :color="$new->getColor()" size="sm">
            {{ $new->getLabel() }}
        </x-filament::badge>
    @endif
</div>
