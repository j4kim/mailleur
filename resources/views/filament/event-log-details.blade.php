@php
    use App\Enums\EventLogType;
    use App\Enums\RecipientStatus;
    use Carbon\Carbon;

    $meta = $eventLog->meta;
@endphp

<div>
    @if ($eventLog->type === EventLogType::StatusChanged)
        @php
            $old = RecipientStatus::from($meta['old']);
            $new = RecipientStatus::from($meta['new']);
        @endphp
        From {{ $old->getLabel() }} to
        <x-filament::badge :color="$new->getColor()" size="sm">
            {{ $new->getLabel() }}
        </x-filament::badge>
    @elseif ($eventLog->type === EventLogType::LinkClicked)
        {{ @$eventLog->meta['url'] }}
    @elseif ($eventLog->type === EventLogType::MailScheduled)
        To be sent at {{ Carbon::make($eventLog->meta['to_be_sent_at'])->format('d.m.Y H:i') }}
    @else
        <x-filament::link :href="route('event-log-details', $eventLog)" target="_blank">
            Details
        </x-filament::link>
    @endif
</div>
