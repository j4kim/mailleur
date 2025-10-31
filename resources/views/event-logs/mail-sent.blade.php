<?php
use function App\Tools\formatAddress;
?>

<h3>Envelope</h3>
<dl>
    @foreach ($meta['envelope'] as $key => $value)
        @empty($value)
            @continue
        @endempty
        <dt>{{ $key }}</dt>
        <dd>
            @if (gettype($value) === 'string')
                {{ $value }}
            @elseif ($key === 'from')
                {{ formatAddress($value) }}
            @else
                <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
            @endif
        </dd>
    @endforeach
</dl>

<h3>Content</h3>
<pre style="width:100%; overflow: auto">
    {{ $meta['content'] }}
</pre>

<h3>Rendered content</h3>
<div style="border: 1px solid lightgray; padding: 1rem">
    {!! $meta['content'] !!}
</div>
