<!DOCTYPE html>
<html lang="en" ng-app="apartment">
    <head>
        @include('admin::includes.head')
    </head>
    <body>
        <div class="container">

            <header class="row">
                @include('admin::includes.header')
            </header>

            <div id="main" class="row">

                    @yield('content')

            </div>

            <footer class="row">
                @include('admin::includes.footer')
            </footer>

        </div>
    </body>
</html>
