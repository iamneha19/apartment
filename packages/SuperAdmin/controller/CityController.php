<?php namespace Packages\SuperAdmin\Controller;
use App\Http\Controllers\Controller;
use Session;
class CityController extends Controller{
     
    public function __construct() {
    //    $this->middleware('auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
	public $layout = 'superadmin::layouts.super_admin_layout';
   
        public function index()
    {
        $content = view('superadmin::city.index');
        return view($this->layout,['content'=>$content]);
    }
    
    /* public function posts()
    {
       Blade::setContentTags('<%', '%>');        // for variables and all things Blade
        Blade::setEscapedContentTags('<%%', '%%>');   // for escaped data
      return view('admin::user.post');
    } */
	
	public function create()
	{
		 /* $name = $request::input();
		dd($request::input());exit; */
		// print_r($_POST);exit;
	}
}



