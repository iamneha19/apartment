<div class="navbar">
    <div class="navbar-inner">
        <h3 class="text-muted pull-left">My Adda</h3>
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
</div>
