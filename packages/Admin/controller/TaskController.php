<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;
class TaskController extends Controller{
     
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
        $content = view('admin::task.index');
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
            $content = view('admin::task.view',['id'=>$id]);
            return view($this->layout, ['content' => $content]);
	}
        
        public function edit($id)
	{
            $user_id = Session::get('user.user_id');
//            print_r($user_id);exit;
            $content = view('admin::task.edit',['user_id'=>$user_id,'id'=>$id]);
            return view($this->layout, ['content' => $content]);
	}
        public function mytasks()
        {
            $content = view('admin::task.mytasks');
            return view($this->layout, ['content' => $content]);
        }
        public function oldTasks()
        {
            $content = view('admin::task.old_tasks');
            return view($this->layout, ['content' => $content]);
        }
}



