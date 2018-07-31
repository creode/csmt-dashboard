<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Creode Dashboard') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- js -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/all.js" integrity="sha384-xymdQtn1n3lH2wcu0qhcdaOpQwyoarkgLVxC/wZ5q7h9gHtxICrpcaSUfygqZGOe" crossorigin="anonymous"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</head>
<body>
    <div id="feed">
        <img src="https://s3-eu-west-2.amazonaws.com/assets.creode.co.uk/wp-content/uploads/2018/06/04091656/block-create1.svg" />
    </div>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <svg width="102" height="23" xmlns="http://www.w3.org/2000/svg"><g fill="#000" fill-rule="evenodd"><path d="M93.7919635 5.57575758c-5.2272344 0-8.5590868 3.59969876-8.5590868 8.62385802C85.2328767 19.4914465 88.5324015 23 94.0860745 23c2.4502222 0 5.226883-.8757874 6.9258395-2.6839227l-2.5482591-2.5812492c-.915012.9712558-2.8423702 1.5746881-4.3122224 1.5746881-2.8096912 0-4.5409754-1.6813245-4.8024074-3.4826149h12.5775499c.620549-6.84526381-2.7439822-10.25114372-8.1346115-10.25114372zm-4.3775804 6.64820262c.588222-2.1615485 2.3518338-3.21530335 4.5082964-3.21530335 2.2868272 0 3.9200744 1.05375485 4.1815064 3.21530335h-8.6898028zM73.9170387 5.67878055c-4.7235826 0-8.2458058 2.95420794-8.2458058 8.66079025C65.6712329 19.7778842 69.2407343 23 73.9971293 23c1.9680418 0 3.9660734-.613083 5.212594-2.5938682l.2589714 2.3407639h3.6682916V0H79.255943v8.33077974c-1.0581136-1.71179417-3.6330094-2.65199919-5.3389043-2.65199919zm.3362394 13.52826485c-2.558666 0-4.5926855-1.9804242-4.5926855-4.8671135 0-2.9874257 2.0340195-4.83389589 4.5926855-4.83389589 2.5255008 0 4.6907701 1.94684539 4.6907701 4.83389589 0 2.9874256-2.1649165 4.8671135-4.6907701 4.8671135zM55.7253624 5.57575758c-2.7527309 0-4.9559846 1.10259707-6.4219697 2.84837578l-8.9099396 9.33707204-.3228445.3303416c-.7176699.657037-1.7261137 1.1113479-2.9458675 1.1113479-2.4409329 0-3.8794797-1.6272787-4.303525-3.6293821h6.7583552l5.1815482-5.3022376c-1.3163221-2.78055736-4.0405457-4.69551762-7.6691617-4.69551762-5.2015033 0-8.4481222 3.93201285-8.4481222 8.71212122C28.6438356 19.1015318 31.8245315 23 37.1250975 23c2.3336743 0 4.2618331-.7562124 5.6896898-2.0130418l.4350918-.4156616L53.5908824 9.8968151c.639275-.53671392 1.3270123-.55822623 2.1683323-.55822623 2.7167405 0 4.4396472 2.44073573 4.4396472 4.94928993 0 2.5424634-1.490929 4.9150161-4.4396472 4.9150161-2.6354948 0-4.1025489-1.8963649-4.3854833-4.1143206l-3.2216751 3.2964881c1.2856768 2.7462835 3.9379196 4.614573 7.6071584 4.614573 5.3009223 0 8.5147579-3.8981036 8.5147579-8.7121212-.0007127-4.77974377-3.3471069-8.71175662-8.5486102-8.71175662zM37.1247411 9.33822425c1.9723451 0 3.4169497 1.28855095 4.0622825 2.95374965h-8.1569919c.5990085-1.6648341 1.9784028-2.95374965 4.0947094-2.95374965z" fill-rule="nonzero"></path><path d="M24.550267 5.57575758c-1.6187602 0-3.2702577.33659548-4.5006965 2.42311964l-.2915022-1.92061753h-3.6895751V23h3.8309728v-9.0108267c0-3.1121288 1.9854887-4.27494663 4.0576968-4.27494663 1.295217 0 2.0777803.37595693 2.8871604 1.09439513l1.7995124-3.62493103c-.8738101-.92407417-2.442071-1.60793319-4.0935686-1.60793319z"></path><path d="M8.90927571 19.0941578c-2.58401547 0-4.76058675-1.6687521-4.76058675-4.806638 0-2.8704117 2.04014696-4.87341689 4.82861603-4.87341689 1.15613201 0 2.38029341.43406225 3.36635221 1.30182769l2.6180301-2.7034646c-1.8360591-1.76927937-3.672484-2.43670842-6.05277734-2.43670842C4.04627929 5.57575758 0 8.44616923 0 14.2878788 0 20.1295884 4.04627929 23 8.90890996 23c2.48233734 0 4.52248424-.7342079 6.46095304-2.6036554l-2.7884691-2.6704343c-1.0197077 1.0013231-2.3455472 1.3682475-3.67211819 1.3682475z" fill-rule="nonzero"></path></g></svg>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        @yield('additional-nav')
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            @yield('content')
        </div>

        @yield('post-content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('page-js')
</body>
</html>
