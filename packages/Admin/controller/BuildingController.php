<?php
namespace Packages\Admin\Controller;

use App\Http\Controllers\Controller;

class BuildingController extends Controller {
	
	public function index() {
		
		return view('admin::buildings.index');
	}
    
    public function acl($id) {
        
		$society_acl = false;  // To check society.acl permission  
        $building_acl = false; // To check building.acl permission
        $mybuilding_acl = true; // To check mybuilding.acl permission
        
		return view('admin::acl.index', ['id' => $id, 'society_acl'=>$society_acl, 'building_acl'=>$building_acl, 'mybuilding_acl'=>$mybuilding_acl]);
	}
}