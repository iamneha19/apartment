<?php

namespace Packages\Admin\Controller;

use App\Http\Controllers\Controller;

class SocietyConfigController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
	 public $layout = 'admin::layouts.admin_layout';

	public function index()
    {
        $createPermission = false;  // To check meeting.create permission
        $updatePermission = false; // To check meeting.update permission
        $listPermission = false; // To check meeting.list permission
        $oldListPermission = false; // To check meeting.list permission

        if(session()->get('acl.admin.admin_meeting')){
            $permissions = session()->get('acl.admin.admin_meeting.permissions');

            if(is_array($permissions)){
                if(in_array('meeting.list', $permissions)){
                    $listPermission = true;
                }

                if(in_array('meeting.create', $permissions)){
                    $createPermission = true;
                }

                if(in_array('meeting.update', $permissions)){
                    $updatePermission = true;
                }

                if(in_array('old_meeting.list', $permissions)){
                    $oldListPermission = true;
                }
            }
        }

        $viewData['createPermission'] = $createPermission;
        $viewData['updatePermission'] = $updatePermission;
        $viewData['listPermission'] = $listPermission;
        $viewData['oldListPermission'] = $oldListPermission;

	   $content = view('admin::society.config', $viewData);
	   return view($this->layout, ['content' => $content]);
    }

    public function import()
    {
    	   $content = view('admin::import.society');
    	   return view($this->layout, ['content' => $content]);
    }
}
