<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;

class TaskCategoryController extends Controller{
     
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
                $society_id = Session::get('user.society_id');
		$content = view('admin::task_category.index',['society_id'=>$society_id]);
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
	public function view($id)
	{
		$content = view('admin::task_category.view',['id'=>$id]);
		return view($this->layout, ['content' => $content]);
	}
	
	public function edit($id)
	{
		$content = view('admin::task_category.edit',['id'=>$id]);
		return view($this->layout, ['content' => $content]);
	}
}



