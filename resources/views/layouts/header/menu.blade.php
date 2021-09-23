<header class="banner">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 bg-light">
                <nav id="main-menu" class="navbar navbar-light navbar-expand-md justify-content-center px-2">
                    <a href="/" class="navbar-brand mr-0">
                        <i class="fas fa-chess-knight"></i>#####
                    </a>
                    <button class="navbar-toggler ml-1" type="button" data-toggle="collapse" data-target="#main-menu-center">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="navbar-collapse collapse justify-content-between align-items-center w-100" id="main-menu-center">
                        <ul class="navbar-nav mx-auto text-center">
                            <li class="nav-item {{ Route::currentRouteName() == 'home' ? 'active' : null }}">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item {{ Route::currentRouteName() == 'convert.index' ? 'active' : null }}">
                                <a class="nav-link" href="{{ route('convert.index') }}">Convert</a>
                            </li>
                            <li class="nav-item {{ Route::currentRouteName() == 'about' ? 'active' : null }}">
                                <a class="nav-link" href="{{ route('about') }}">About</a>
                            </li>
                        </ul>
                        <!-- <ul class="nav navbar-nav flex-row justify-content-center flex-nowrap">
                            <li class="nav-item"><a class="nav-link" href=""><i class="fa fa-facebook mr-1"></i></a> </li>
                            <li class="nav-item"><a class="nav-link" href=""><i class="fa fa-twitter"></i></a> </li>
                        </ul> -->
                    </div>
                </nav>

            </div>
        </div>
    </div>
</header>
