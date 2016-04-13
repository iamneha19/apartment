<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;
class FlatReportsController extends Controller{
     
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
        $content = view('admin::flatreports.index');
        return view($this->layout,['content'=>$content]);
    }
    
    /* public function posts()
    {
       Blade::setContentTags('<%', '%>');        // for variables and all things Blade
        Blade::setEscapedContentTags('<%%', '%%>');   // for escaped data
      return view('admin::user.post');
    } */
	
	
}



