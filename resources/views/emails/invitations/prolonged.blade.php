@component('mail::message')
# Hello {{ $emailHandle }},

Your confirmation token got prolonged to {{ $validUntil }}

@component('mail::button', ['url' => $continueUrl])
Continue to {{ config('yap.short_name') }}
@endcomponent

@component('mail::subcopy')
If you’re having trouble clicking the "Open sesame" button, copy and paste the URL below
into your web browser: [{{ $continueUrl }}]({{ $continueUrl }})
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
