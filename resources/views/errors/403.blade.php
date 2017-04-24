@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>#403</strong><br>Access forbidden!</div>
            @include('errors.link-back')
        </div>
    </div>
@stop
