<a href="#" class="site-logo">
    <img src="{{asset('assets/img/logo.png')}}" alt="">
</a>

<button id="show-hide-sidebar-toggle" class="show-hide-sidebar">
    <span>toggle menu</span>
</button>

<button class="hamburger hamburger--htla">
    <span>toggle menu</span>
</button>
<div class="site-header-content">
    <div class="site-header-content-in">
        <div class="site-header-shown">

            <div class="dropdown user-menu">
                <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{{asset('assets/img/avatar-2-64.png')}}" alt="">
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                    <a class="dropdown-item" href="{{route('profile.index')}}"><span class="font-icon glyphicon glyphicon-user"></span>Profile</a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}"
                       class="dropdown-item"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        <span class="font-icon glyphicon glyphicon-log-out"></span>Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div><!--.site-header-shown-->

        <div class="mobile-menu-right-overlay"></div>
        <div class="site-header-collapsed">
            <div class="site-header-collapsed-in">


            </div><!--.site-header-collapsed-in-->
        </div><!--.site-header-collapsed-->
    </div><!--site-header-content-in-->
</div><!--.site-header-content-->