@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>Error #500</strong><br>Oops, Ytrium is trying too hard!</div>
            @include('errors.link-back')
        </div>
    </div>
@stop
