@component('mail::message')
# Welcome aboard {{ $user->name ?? $user->username ?? emailHandle($user->email) }}!

You have been granted access to {{ config('yap.short_name') }}.

@component('mail::button', ['url' => $continueUrl])
Take me to {{ config('yap.short_name') }}!
@endcomponent

@if($user->is_admin)
    {{--TODO: make guide link to administrator options--}}
    Additionally you are an administrator, you may find useful following [guide]({{route('docs', ['page' => 'administration'])}}).
@endif

@component('mail::subcopy')
If you’re having trouble clicking the "Take me to {{ config('yap.short_name') }}!" button, copy and paste the URL below
into your web browser: [{{ $continueUrl }}]({{ $continueUrl }})
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
