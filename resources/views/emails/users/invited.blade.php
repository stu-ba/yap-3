@component('mail::message')
# Welcome {{ $emailHandle }},

you have been invited to {{ config('yap.short_name') }}

@component('mail::button', ['url' => $continueUrl])
Continue to {{ config('yap.short_name') }}
@endcomponent

@component('mail::subcopy')
If you’re having trouble clicking the "Continue to {{ config('yap.short_name') }}" button, copy and paste the URL below
into your web browser: [{{ $continueUrl }}]({{ $continueUrl }})
<br><br>
@if($validUntil === null)
Your confirmation token is valid until you successfully sign in to {{ config('yap.short_name') }}.
@else
Your confirmation token is valid until you successfully sign in to {{ config('yap.short_name') }} or until {{ $validUntil }}.
@endif
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
