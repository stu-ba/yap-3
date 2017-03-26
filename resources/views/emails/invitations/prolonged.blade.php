@component('mail::message')
# Hello {{ $emailHandle }},

your confirmation token just got prolonged, it will expire at {{ $validUntil }}.

@component('mail::button', ['url' => $continueUrl])
Continue to {{ config('yap.short_name') }}
@endcomponent

@component('mail::subcopy')
If you’re having trouble clicking the "Continue to {{ config('yap.short_name') }}" button, copy and paste the URL below
into your web browser: [{{ $continueUrl }}]({{ $continueUrl }})
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
