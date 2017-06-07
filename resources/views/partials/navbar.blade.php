<nav class="navbar navbar-transparent navbar-absolute">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ route('users.notifications') }}">
                        <i class="fa {{ fa('notification') }}"></i>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="notification">{{ $unreadNotificationsCount }}</span>
                        @endif
                        <p class="hidden-lg hidden-md">Notifications</p>
                    </a>
                </li>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa {{ fa('user') }}"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('profile') }}"><i class="fa fa-lg {{ fa('profile') }}"></i> <span>Profile</span></a></li>
                        <li><a href="{{ route('switch.taiga') }}">{!! svg('taiga-icon', 'taiga-icon fa-lg') !!} <span>Switch to Taiga</span></a></li>
                        <li><a href="{{ route('logout') }}"><i class="fa fa-lg {{ fa('logout') }}"></i> <span>Logout</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
