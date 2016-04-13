<!DOCTYPE html>
<html lang="en" ng-app="frontend">
    <head>
        @include('includes.head')
    </head>
    <body>
        <div class="container">

            <header class="row">
                @include('includes.header')
            </header>

            <div id="main" class="row">

                <!-- sidebar content -->
                <div id="sidebar" class="col-md-2">
                    @section('sidebar')
                        
                    @show
                     @include('includes.sidebar')
                </div>

                <!-- main content -->
                <div id="content" class="col-md-10">
                    @yield('content')
                </div>

            </div>

            <footer class="row">
                @include('includes.footer')
            </footer>

        </div>
    </body>
</html>
