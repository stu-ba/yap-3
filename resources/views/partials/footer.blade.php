<footer class="footer">
    <div class="container-fluid">
        <nav class="pull-left">
            <ul>
                <li>
                    <a href="{{ route('home') }}">
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs') }}">
                        User guide
                    </a>
                </li>
                <li>
                    <a href="#">
                        Switch to Taiga
                    </a>
                </li>
                <li>
                    <a href="{{ route('logout') }}">
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
        <p class="copyright pull-right">
            Source code is hosted on <a href="{{ config('yap.github.source_code') }}" class="external color-black"><i class="fa {{ fa('github') }}"></i></a>.
        </p>
    </div>
</footer>
