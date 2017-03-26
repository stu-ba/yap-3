@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>#403</strong><br>Access forbidden!</div>
            <br>
            @unless(url()->previous() == url()->current())
                <a class="link_err" href="{{ url()->previous() }}">Please, take me back :(</a>
            @endunless
        </div>
    </div>
@stop