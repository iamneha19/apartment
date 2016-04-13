 <!-- sidebar nav -->
<?php $route_name = Route::current()->getName() ?> 
<nav id="sidebar-nav">
    <ul class="nav nav-pills nav-stacked">
        <li class='{{ (in_array($route_name, ['myflat'])) ? 'active' :''  }}' >
            <a href='{{ route('myflat')}}'>
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                My Flat
            </a>
        </li>
        <li class='{{ (in_array($route_name, ['notice'])) ? 'active' :''  }}' >
            <a href='{{ route('notice')}}'>
                <span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span>
                Noticeboard
            </a>
        </li>
        <li class='{{ (in_array($route_name, ['folders','files'])) ? 'active' :''  }}'>
            <a href='{{ route('folders')}}'>
                <span class="glyphicon glyphicon-file" aria-hidden="true"></span>
                Documents
            </a>
        </li>
    </ul>
</nav>

