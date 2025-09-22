<x-mail::message>
Hi,

{{ $inviter->name }} has invited you to join a "{{ $team->name }}" team on {{ config('app.name') }}.

If it is the first time for you on the app, you will need to reset your password.
To do this, click the "Forgot password?" link in the login form.

<x-mail::button :url="route('filament.admin.pages.dashboard', $team)">
    Join
</x-mail::button>

See you !<br>
</x-mail::message>