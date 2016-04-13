<?php namespace App\Http\Controllers;
use Session;
class UserController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| User Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

        public $layout = 'layouts.default';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('checkSession');
	}

	/**
	 * User Index.
	 *
	 * @return index
	 */
	public function index()
	{
                return 'User Index';
	}

    public function logout()
    {
        Session::flush();
        return redirect()->route('home');
    }

    public function myflat()
    {
       return $this->members();
    }

    public function members()
    {
        $flat_id = Session::get('user.flat_id');
        if($flat_id){
            $content = view('user.members',['flat_id'=>$flat_id]);
            return view($this->layout, ['content' => $content]);
        }else{
            $content = view('user.add_flat');
            return view($this->layout, ['content' => $content]);
        }

    }

     
	public function switchSociety()
	{
            $user = \Input::get('user',null);
            $acl = \Input::get('acl',null);
            
            $redirect_url = route('conversations');
           
           if(empty($acl['resident']) && !empty($acl['admin'])){
               $redirect_url = route('admin.dashboard');
           }
            
            Session::put('user', $user);
            Session::put('acl', $acl);
            $arr = array('success' => true,'redirect_url'=>$redirect_url);
            return json_encode($arr);

	}

    public function updateFlat()
	{
            $flat_id = \Input::get('flat_id',null);
            Session::put('user.flat_id', $flat_id);
            $arr = array('success' => true);
            return json_encode($arr);

	}

    public function user_personalInfo()
    {
        $user_id = Session::get('user.user_id');
        $content = view('user.personal_info',['user_id'=>$user_id]);
        return view($this->layout, ['content' => $content]);
    }
    
    public function warning()
    {
        $content = view('user.warning');
        return view($this->layout, ['content' => $content]); 
    }        



}
