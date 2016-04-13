<div class="navbar">
    <div class="navbar-inner">
        <h3 class="text-muted pull-left">{{env('PROJECT_NAME')}}</h3>
        <ul class="nav nav-pills pull-right">
            <li><a href="#">Hi </a></li>
            <li role="presentation" class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                  Dropdown <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="{{ route('logout') }}">Log Out</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
