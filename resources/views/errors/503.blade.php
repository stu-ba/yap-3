@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong># 503</strong><br>Service is not available, we will be back soon!</div>
            @include('errors.link-back')
        </div>
    </div>
@stop