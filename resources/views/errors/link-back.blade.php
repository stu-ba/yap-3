{{--@unless(url()->previous() == url()->current())--}}
    {{--Try <a class="link_err" href="{{ url()->previous() }}">harder</a> or go <a class="link_err" href="{{ route('profile') }}">home</a>.--}}
{{--@else--}}
    {{--No suggestion for you.--}}
{{--@endunless--}}
Take me <a class="link_err" href="{{ route('profile') }}">home</a>.