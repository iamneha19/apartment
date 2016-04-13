<nav class="menu">
<ul class="nav navbar-nav home-menu-ul ">
                            <li class=""><a href="{{ route('admin.helpdesk')}}">Home</a></li>

                            <li><a href="{!! url('features?visitor=❌') !!}">Features</a></li>
							<?php if(array_key_exists('manage_user', $modules)){ ?>
								<li class='{{ (in_array($routeName, array('admin.users','admin.user.edit','admin.user.flat_edit'))) ? 'active' : '' }}'>
									<a href="{{ route('admin.users') }}"><i class="fa fa-user_old"></i> Users</a>
								</li>
                            <?php } ?>
								<li class='{{ (in_array($routeName, ['admin.flat'])) ? 'active' : '' }}'>
									<a href="{{ route('admin.flat') }}">Flat</a>
								</li>
							<?php if(array_key_exists('admin_acl', $modules)){ ?>
                                <li class='{{ (in_array($routeName, array('admin.acl'))) ? 'active' : '' }}'>
									<a href="{{ route('admin.acl') }}"><i class="fa fa-unlock_old"></i> Access Control</a>
								</li>
                            <?php } ?>
							@if(array_key_exists('admin_society_info', $modules))
                            <li class='{{ (in_array($routeName, array('admin.society_info'))) ? 'active' : '' }}'>
                                        <a href="{{ route('admin.society_info') }}"><i class="fa fa-square-o_old"></i> Society Info</a>
                            </li>
                            @endif
							<?php if(array_key_exists('manage_building', $modules)){ ?>
								<li class='{{ (in_array($routeName, array('admin.buildings'))) ? 'active' : '' }}'>
									<a href="{{ route('admin.buildings') }}"><i class="fa fa-square-o_old"></i> Buildings & Blocks</a>
								</li>
                            <?php } ?>

                    </ul>
</nav>


<!--<div class="navbar">
    <div class="navbar-inner">
        <h3 class="text-muted pull-left">{{env('PROJECT_NAME')}}</h3>
        @include('admin::includes.society_dropdown')
        <ul class="nav nav-pills pull-right">
            <li class="active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
			<li><a href="{{ route('admin.users') }}">Users</a></li>
			<li><a href="{{ route('admin.meeting') }}">Meetings</a></li>
			<li><a href="{{ route('admin.task_category') }}">Task Category</a></li>
			<li><a href="{{ route('admin.task') }}">Task</a></li>
			<li><a href="{{ route('admin.block') }}">Blocks</a></li>
		</ul>
    </div>
</div>-->
