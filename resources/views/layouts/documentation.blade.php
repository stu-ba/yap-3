<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="author" content="Martin Kiesel">
    <meta name="description" content="Yap - User Guide">
    <meta name="keywords" content="yap, yap-3, user guide">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ isset($title) ? $title . ' - ' : null }} Yap User Guide</title>

    <!--[if lte IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link href="{{ mix('/css/docs.css') }}" rel="stylesheet"/>
    {{--<link rel="apple-touch-icon" href="/favicon.png">--}}
</head>
<body class="@yield('body-class', 'docs') language-php">
<span class="overlay"></span>
<nav class="main">
    <div class="fill-left nav-block"></div>
    <ul class="main-nav">
        @include('partials.docs-nav')
    </ul>

    <div class="responsive-sidebar-nav">
        <a href="#" class="toggle-slide menu-link btn">&#9776;</a>
    </div>
</nav>
@yield('content')
<footer class="main">
    <ul>
        @include('partials.docs-nav')
    </ul>
</footer>
<script src="{{ mix('/js/docs.js') }}"></script>
</body>
</html>
