<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;
class ParkingController extends Controller{

    public function __construct() {
    //    $this->middleware('auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
	public $layout = 'admin::layouts.admin_layout';
    public function index()
    {
//        print_r("hello");exit;
        $content = view('admin::parking.index');
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
    
    public function setup()
    {
        $content = view('admin::parking.setup');
        return view($this->layout,['content'=>$content]); 
    }        
}
