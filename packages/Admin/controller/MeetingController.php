<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;

class MeetingController extends Controller{
     
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
        }else{
            
        }
        
        $viewData['createPermission'] = $createPermission;
        $viewData['updatePermission'] = $updatePermission;
        $viewData['listPermission'] = $listPermission;
        $viewData['oldListPermission'] = $oldListPermission;
        
	   $content = view('admin::meeting.index',$viewData);
	   return view($this->layout, ['content' => $content]);
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
	
	public function edit($id)
	{
		
		$content = view('admin::meeting.edit',['id'=>$id]);
		return view($this->layout, ['content' => $content]);
	}
	
	public function view($id)
	{
		$content = view('admin::meeting.view',['id'=>$id]);
		return view($this->layout, ['content' => $content]);
	}
        
        public function oldMeetings()
	{
		$content = view('admin::meeting.old_meeting');
		return view($this->layout, ['content' => $content]);
	}
}



