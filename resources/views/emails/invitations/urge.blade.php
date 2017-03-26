@component('mail::message')
# Hey {{ $emailHandle }}!

Hurry up and confirm your bond with {{ config('yap.short_name') }}.

@component('mail::button', ['url' => $continueUrl])
Open sesame
@endcomponent

@component('mail::subcopy')
If you’re having trouble clicking the "Open sesame" button, copy and paste the URL below
into your web browser: [{{ $continueUrl }}]({{ $continueUrl }})
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
