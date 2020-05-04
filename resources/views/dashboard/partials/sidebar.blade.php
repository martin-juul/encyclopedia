<nav id="sidebar" class="sidebar-container">
    <div class="sidebar-content">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard.home') }}">{{ config('app.name') }}</a>
        </div>
        <div class="sidebar-header">
            <div class="user-info">
                <span class="user-name">{{ $user->name }}</span>
                <span class="user-role">{{ $user->getRoleName() }}</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li class="header-menu">
                    <span>General</span>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fa fa-folder"></i>
                        <span>Articles</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="#">
                                    Overview
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Import
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="header-menu">
                    <span>System</span>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-book"></i>
                        <span>Profiling</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-calendar"></i>
                        <span>Metrics</span>
                    </a>
                </li>

                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fa fa-folder"></i>
                        <span>Panels</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ config('horizon.path') }}">
                                    Horizon
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <!-- sidebar-menu  -->
    </div>
    <!-- sidebar-content  -->
    <div class="sidebar-footer">
        <a href="#">
            <i class="fa fa-bell"></i>
            <span class="badge badge-pill badge-warning notification">3</span>
        </a>

        <a href="#">
            <i class="fa fa-cog"></i>
            <span class="badge-sonar"></span>
        </a>

        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"></i>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST"
              style="display: none;">
            @csrf
        </form>
    </div>
</nav>
