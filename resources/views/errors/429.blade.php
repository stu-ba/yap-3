@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong># 429</strong><br>Too many requests, slow down!</div>
            @include('errors.link-back')
        </div>
    </div>
@stop
