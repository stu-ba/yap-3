@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong># 500</strong><br>Oops, Server is trying too hard!</div>
            @include('errors.link-back')
        </div>
    </div>
@stop