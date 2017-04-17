<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <meta name="viewport" content="width=device-width"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="yap-token" content="{{ yap_token() }}">

    <title>{{ $title ?? 'Yap 3.0' }}</title>

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
<div class="wrapper">
    @include('partials.sidebar')
    <div class="main-panel">
        @include('partials.navbar')
        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        @include('partials.footer')
    </div>
</div>
</body>
<script src="{{ mix('/js/app.js') }}"></script>
</html>
