<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Title -->
    <title>{{ $title ?? 'Error' }}</title>

    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300|700" rel="stylesheet" type="text/css">
    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #309cd4;
            display: table;
            font-family: 'Source Sans Pro', sans-serif;
            color: white;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content_err {
            text-align: center;
            display: inline-block;
            font-size: 32px;
        }

        .title_err {
            font-size: 72px;
            margin-bottom: 40px;
        }

        a.link_err, a:visited {
            font-size: 32px;
            text-decoration: underline;
            color: white;
        }
    </style>
</head>
<body class="flat-blue login-page register-page">
@yield('content')
</body>
</html>
