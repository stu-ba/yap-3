@extends('layouts.error')
@section('content')
    <div class="container">
        <div class="content_err">
            <div class="title_err"><strong>#401</strong><br>Unauthorized: Access is denied due to invalid credentials.</div>
            @include('errors.link-back')
        </div>
    </div>
@stop
