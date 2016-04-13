

{{--*/  $routeName = Route::getCurrentRoute()->getName() /*--}}
<div class="col-lg-12">
    <!--<div id="copyright" class="text-left"> &copy; Copyright 2016 {{env('PROJECT_NAME')}}</div>-->
    <div class="footeradmin">
    	<div class="bottom-link-grid">
        	<div class="row grid-920">
            	<div class="col-sm-3 col-xs-6">
                	<h2 class="widgettitle">About Us</h2>
                    <div class="">
                    	<ul class="menu">
                        	<li><a href="#">Profile</a></li>
                            <li><a href="#">Vision &amp; Mission</a></li>
                            <li><a href="#">News</a></li>
                            <li><a href="#">Partners</a></li>
                            <li><a href="#">Credentials</a></li>
                            <li><a href="#">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                	<h2  class="widgettitle">Communication</h2>
                    <div class="">
                    	<ul class="menu">
                            <?php if(Session::get('acl.resident') && strtolower(Session::get('role_name'))!='admin'){ ?>
                        	   <li><a href="{{ route('conversations') }}">Conversation &amp; Groups</a></li>
							   <li><a href="{{ route('notice') }}">Notice Board</a></li>
                            <?php } ?>   
                            <?php if(Session::get('acl.admin') ){ ?>
							   <li><a href="{{ route('admin.noticeboard') }}">Notice</a></li>
							<?php } ?>
							<?php if(Session::get('acl.resident') && strtolower(Session::get('role_name'))!='admin'){ ?>
								<li><a href="{{ route('events') }}">Events</a></li>
								<li><a href="{{ route('document.resident') }}">Documents</a></li>
								<li><a href="{{ route('albums') }}">Photo Gallery</a></li>
							<?php } ?> 
							<?php if(Session::get('acl.admin') ){ ?>
								<li><a href="#">Member List</a></li>
							<?php } ?>
                            <?php if(Session::get('acl.resident') && strtolower(Session::get('role_name'))!='admin'){ ?>
								<li><a href="{{ route('members') }}">My Flat</a></li>
							<?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                	<h2 class="widgettitle">Management</h2>
                    <div class="">
                    	<ul class="menu">
							<?php if(Session::get('acl.admin') ){ ?>
								<li><a href="{{ route('admin.users') }}">Users</a></li>
								<li><a href="{{ route('admin.forums') }}">Forums</a></li>
								<li><a href="{{ route('admin.society_files') }}">Files</a></li>
								<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
							<?php } ?>
                            <?php if(Session::get('acl.resident') && strtolower(Session::get('role_name'))!='admin'){ ?> 
								<li><a href="{{ route('helpdesk') }}">Dashboard</a></li>
							<?php } ?>
							<?php if(Session::get('acl.admin') ){ ?>
								<li><a href="{{ route('admin.meeting') }}">Meetings</a></li>
								<li><a href="{{ route('admin.task') }}">Tasks</a></li>
							<?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                	<h2 class="widgettitle">SMS</h2>
                    <div class="textwidget">A Wing 601 & 602,Vertex Vikas Building,<br>S. Radha Krishna Road,Andheri East Mumbai<br><a href="mailto:info@sahakar.net">info@sahakar.net</a></div>
                </div>
            </div>
        </div>
        <div class="site-info">
        	<div class="execphpwidget">&copy; Copyrights 2015, SMS, All rights reserved.</div>
        </div>
    </div>
</div>

