<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;

class UserController extends Controller{

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
        $createPermission = false;  // To check user.create permission
        $listPermission = false; // To check user.list permission
        $updatePermission = false; // To check user.update permission
        $activatePermission = false; // To check user.activate permission
        $deactivatePermission = false; // To check user.deactivate permission
        $approvePermission = false; // To check user.approve permission

        $society_id = session()->get('user.society_id');
        if(session()->get('acl.admin.manage_user')){
            $permissions = Session::get('acl.admin.manage_user.permissions');
            if(is_array($permissions)){
                if(in_array('user.list', $permissions)){
                    $listPermission = true;
                }

                if(in_array('user.create', $permissions)){
                    $createPermission = true;
                }

                if(in_array('user.update', $permissions)){
                    $updatePermission = true;
                }

                if(in_array('user.activate', $permissions)){
                    $activatePermission = true;
                }

                if(in_array('user.deactivate', $permissions)){
                    $deactivatePermission = true;
                }

                if(in_array('user.approve', $permissions)){
                    $approvePermission = true;
                }
            }
        }else{

        }

        $viewData['createPermission'] = $createPermission;
        $viewData['listPermission'] = $listPermission;
        $viewData['updatePermission'] = $updatePermission;
        $viewData['activatePermission'] = $activatePermission;
        $viewData['approvePermission'] = $approvePermission;
        $viewData['deactivatePermission'] = $deactivatePermission;
        $viewData['society_id'] = $society_id;

        $content = view('admin::user.index',$viewData);

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
            $society_id = Session::get('user.society_id');
            $content = view('admin::user.edit',['society_id'=>$society_id,'id'=>$id]);
            return view($this->layout, ['content' => $content]);
        }

        public function flat_edit($id)
        {
//            $society_id = Session::get('user.society_id');
            $content = view('admin::user.flat_edit',['id'=>$id]);
            return view($this->layout, ['content' => $content]);
        }

        public function user_flat_edit($id)
        {
            $content = view('admin::user.user_flat_edit',['id'=>$id]);
            return view($this->layout, ['content' => $content]);
        }
		
		public function user_personalInfo()
		{
			$user_id = Session::get('user.user_id');
			$content = view('user.personal_info',['user_id'=>$user_id]);
			return view($this->layout, ['content' => $content]);
		}
		
		public function changepwd()
		{
		  // return view('admin::user.index');
					$email = Session::get('user.email');
			$content = view('user.change_pwd',['user_email'=>$email]);
			return view($this->layout, ['content' => $content]);
		}
    
		public function reset_forgotPwd()
		{
		  // return view('admin::user.index');
					$email = Session::get('user.email');
			$content = view('user.reset_forgotPwd',['user_email'=>$email]);
			return view($this->layout, ['content' => $content]);
		}
}
