@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>Error #404</strong><br>Page not found!</div>
            @include('errors.link-back')
        </div>
    </div>
@stop
