@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>Error #400</strong><br>Bad request!</div>
            @include('errors.link-back')
        </div>
    </div>
@stop
