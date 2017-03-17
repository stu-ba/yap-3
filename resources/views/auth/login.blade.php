<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <meta name="viewport" content="width=device-width"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
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
                            <button class="btn btn-github btn-default" rel="tooltip" data-placement="top" title="Login via GitHub" data-original-title="Login via GitHub"><i class="fa fa-github"></i></button>
                        </div>
                    </div>
                    {{--<form>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<div class="form-group label-floating">--}}
                                    {{--<label class="control-label">Username</label>--}}
                                    {{--<input type="text" class="form-control" required>--}}
                                    {{--<p class="help-block">You can also use email.</p>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<div class="form-group label-floating">--}}
                                    {{--<label class="control-label">Password</label>--}}
                                    {{--<input type="password" class="form-control" required>--}}
                                    {{--<p class="help-block">Yap does not use HTTPS consider using GitHub login.</p>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<div class="togglebutton">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" checked=""> Remember me--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-6">--}}
                                {{--<button class="btn btn-github btn-default" rel="tooltip" data-placement="top" title="" data-original-title="Login with GitHub"><i class="fa fa-github"></i></button>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-6">--}}
                                {{--<button class="btn btn-default pull-right">Login</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <span class="pull-right">Made with <i class="fa fa-heart text-danger"></i>.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

</body>
<script src="{{ mix('/js/app.js') }}"></script>
</html>
