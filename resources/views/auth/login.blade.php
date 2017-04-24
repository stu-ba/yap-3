<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <meta name="viewport" content="width=device-width"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="yap-token" content="{{ yap_token() }}">

    <title>{{ config('yap.short_name') }} - login</title>

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="section section-full-screen section-signup">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="card">
                    <div class="card-header" data-background-color="green">
                        <div class="text-center">
                            <i class="material-icons md-90">fingerprint</i>
                            <h3>Login to Yap 3.0</h3>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('login.github') }}"><button class="btn btn-github btn-default" rel="tooltip" data-placement="top"
                                        title="Login via GitHub" data-original-title="Login via GitHub"><i
                                            class="fa fa-github"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <span class="pull-left"><a href="{{ route('docs') }}"><i class="fa {{ fa('help') }}"></i> User guide</span></a><span class="pull-right">Made with <i class="fa fa-heart text-danger"></i>.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
{{--<script src="{{ mix('/js/app.js') }}"></script>--}}
</html>
