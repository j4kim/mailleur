<x-mail::message>
Your campaign <a href="{{ $campaignUrl }}">{{ $campaign->subject }}</a> was scheduled to be sent to
{{ str('recipient')->plural($recipients->count(), true) }}.

Here is a little report of what happened.

@if($successRecipients->count())
Sending succeeded for {{ str('recipient')->plural($successRecipients->count(), true) }}:

@foreach ($successRecipients as $r)
    - {{ $r->email }}
@endforeach
@endif

@if($failedRecipients->count())
Sending failed for {{ str('recipient')->plural($failedRecipients->count(), true) }}:

@foreach ($failedRecipients as $r)
    - {{ $r->email }}
@endforeach
@endif
</x-mail::message>
