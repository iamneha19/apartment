<?php 
namespace Packages\Admin\Controller;

use App\Http\Controllers\Controller;

class TypeController extends Controller {

  public function __construct() {
//    $this->middleware('auth');
  }

  public function index()
  {
	    $createPermission = false;  
            $listPermission = false; 
	     if(session()->get('acl.admin.admin_setup')){
              $permissions = session()->get('acl.admin.admin_setup.permissions');
          if(is_array($permissions)){
		  
			  if(in_array('type.create', $permissions)){
                    $createPermission = true;
                }
			  if(in_array('type.list', $permissions)){
                    $listPermission = true;
                }
           }
		   
		}
		$viewData['createPermission'] = $createPermission;
		        $viewData['listPermission'] = $listPermission;

     return   $content = view('admin::category.Index',$viewData);
  }
}

