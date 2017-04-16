@unless(url()->previous() == url()->current())
    Try <a class="link_err" href="{{ url()->previous() }}">harder</a> or go <a class="link_err" href="{{ route('home') }}">home</a>.
@else
    No suggestion for you.
@endunless

