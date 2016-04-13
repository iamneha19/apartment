<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;

class BlockController extends Controller{
     
    public function __construct() {
    //    $this->middleware('auth');
    }
	 public $layout = 'admin::layouts.admin_layout';
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index()
    {
	  $content = view('admin::block.index');
	  return view($this->layout, ['content' => $content]);
    }
    
    /* public function posts()
    {
       Blade::setContentTags('<%', '%>');        // for variables and all things Blade
        Blade::setEscapedContentTags('<%%', '%%>');   // for escaped data
      return view('admin::user.post');
    } */
}



