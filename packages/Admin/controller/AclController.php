<?php
namespace Packages\Admin\Controller;

use App\Http\Controllers\Controller;
use Session;

class  AclController extends Controller {

	public function Index() {
        
        $society_acl = false;  // To check society.acl permission  
        $building_acl = false; // To check building.acl permission
        $mybuilding_acl = false; // To check mybuilding.acl permission
        $building_id = null;

        $acl_permissions = Session::get('acl.admin.admin_acl.permissions');       
        if(is_array($acl_permissions)){
            if(in_array('mybuilding.acl', $acl_permissions)){ 
                $building_id = Session::get('acl.building_id');
                $mybuilding_acl = true;
            }
            
            if(in_array('building.acl', $acl_permissions)){ 
//                $building_id = Session::get('acl.building_id');
                $building_acl = true;
            }

            if(in_array('society.acl', $acl_permissions)){
               $building_id = null;
               $society_acl = true;
            }
        }
        
        
		return view('admin::acl.index', ['id' => $building_id, 'society_acl'=>$society_acl, 'building_acl'=>$building_acl, 'mybuilding_acl'=>$mybuilding_acl]);
	}
}
