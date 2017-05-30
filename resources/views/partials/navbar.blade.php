<nav class="navbar navbar-transparent navbar-absolute">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            {{--TODO: remove me--}}
            {{--@if($title ?? false)--}}
                {{--<a class="navbar-title" href="{{ url()->current() }}">{{ $title ?? 'Title is missing and you should never see this :)' }}</a>--}}
            {{--@endif--}}
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                {{--<li>--}}
                    {{--<a href="{{ route('profile') }}">--}}
                        {{--<i class="fa {{ fa('home') }}"></i>--}}
                    {{--</a>--}}
                {{--</li>--}}
                <li>
                    <a href="{{ route('users.notifications') }}">
                        <i class="fa {{ fa('notification') }}"></i>
                        @if($unreadNotificationsCount > 0)
                            <span class="notification">{{ $unreadNotificationsCount }}</span>
                        @endif
                        <p class="hidden-lg hidden-md">Notifications</p>
                    </a>
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li><a href="#" class="alert-info">Empty</a></li>--}}
                    {{--</ul>--}}
                </li>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa {{ fa('user') }}"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('profile') }}"><i class="fa fa-lg {{ fa('profile') }}"></i> <span>Profile</span></a></li>
                        <li><a href="{{ route('switch') }}">{!! svg('taiga-icon', 'taiga-icon fa-lg') !!} <span>Switch to Taiga</span></a></li>
                        <li><a href="{{ route('logout') }}"><i class="fa fa-lg {{ fa('logout') }}"></i> <span>Logout</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
