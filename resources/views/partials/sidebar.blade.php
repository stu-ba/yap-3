{{--<div class="sidebar" data-color="green" data-image="/img/sidebar-1.jpg"> purple | blue | green | orange | red--}}
<div class="sidebar">
    <div class="logo">
        <a href="{{ route('profile') }}" class="simple-text">
            {{ config('app.name') }}
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="{{ set_active_paths(['profile*']) }}">
                <a href="{{ route('profile') }}">
                    <i class="fa fa-lg {{ fa('profile') }}"></i>
                    <p>Profile</p>
                </a>
            </li>
            <li class="{{ set_active_paths(['users*', 'invitations*']) }}">
                <a href="{{ route('users.index') }}">
                    <i class="fa fa-lg {{ fa('users') }}"></i>
                    <p>Users{!! set_active_paths('invitations*', ' <span class="fa fa-angle-right"></span> Invitations') !!}</p>
                </a>
            </li>
            <li class="{{ set_active_paths(['projects*']) }}">
                <a href="#">
                    <i class="fa fa-lg {{ fa('project') }}"></i>
                    <p>Projects</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="notification">2</span>
                    <i class="fa fa-lg {{ fa('notification') }}"></i>
                    <p>Notifications</p>
                </a>
            </li>
            <li class="{{ set_active_paths(['system-actions*']) }}">
                <a href="#">
                    <i class="fa fa-lg {{ fa('system') }}"></i>
                    <p>System Actions</p>
                </a>
            </li>
            <li>
                <a href="{{ route('switch') }}">
                    <i>{!! svg('taiga-icon', 'taiga-icon fa-lg') !!}</i>
                    <p>Switch to Taiga</p>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}">
                    <i class="fa fa-lg {{ fa('logout') }}"></i>
                    <p>Log out</p>
                </a>
            </li>
            <li class="user-guide">
                <a href="{{ route('docs') }}">
                    <i class="fa fa-lg {{ fa('help') }}"></i>
                    <p>User Guide</p>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-background"></div>
</div>
