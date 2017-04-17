{{--<div class="sidebar" data-color="green" data-image="/img/sidebar-1.jpg"> purple | blue | green | orange | red--}}
<div class="sidebar">
    <div class="logo">
        <a href="{{ route('home') }}" class="simple-text">
            {{ config('app.name') }}
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="{{ set_active_paths(['home*']) }}">
                <a href="#">
                    <i class="fa fa-lg fa-home"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="{{ set_active_paths(['users*', 'profile*', 'invitations*']) }}">
                <a href="{{ route('users.index') }}">
                    <i class="fa fa-lg fa-users"></i>
                    <p>Users</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="notification">5</span>
                    <i class="fa fa-lg fa-bell"></i>
                    <p>Notifications</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <i>{!! svg('taiga-icon', 'taiga-icon taiga-icon fa-lg') !!}</i>
                    <p>Switch to Taiga</p>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}">
                    <i class="fa fa-lg fa-sign-out"></i>
                    <p>Log out</p>
                </a>
            </li>
            <li class="user-guide">
                <a href="{{ route('docs') }}">
                    <i class="fa fa-lg fa-support"></i>
                    <p>User Guide</p>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-background"></div>
</div>
