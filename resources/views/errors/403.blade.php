@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>Error #403</strong><br>Access forbidden!<br>
            {{ $exception->getMessage() ?? ''}}
            </div>
            @include('errors.link-back')
        </div>
    </div>
@stop
