@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>Error #429</strong><br>Too many requests, slow down!<br>
                <small>Try again in {{ \Carbon\Carbon::now()->addSeconds($exception->getHeaders()['Retry-After'] ?? 0)->diffForHumans() }}.</small></div>

            @include('errors.link-back')
        </div>
    </div>
@stop
