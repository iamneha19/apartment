<?php namespace App\Http\Controllers;
use Session;

class SiteController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return 'index';
	}
	
	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function login()
	{
           
            $access_token = \Input::get('access_token',null);
            $user = \Input::get('user',null);
            $socities = \Input::get('socities',null);
            $acl = \Input::get('acl',null);
            $role_name = \Input::get('role_name',null); 
			$role_name =strtolower($role_name );
            if(in_array($role_name,array("member,chairperson","chairperson,member","member,chairman","chairman,member","chairman" ))) 
			{ 
                $redirect_url = 'dashboard/admin';
            }
			else 
			{
                        if(strtolower($role_name)!="admin")
                          $redirect_url = 'dashboard/helpdesk';
                         else
                          $redirect_url = 'dashboard/admin';

               if(empty($acl['resident']) && !empty($acl['admin'])){
                   $redirect_url = 'dashboard/admin';
                }
            }
            Session::put('access_token', $access_token);
            Session::put('user', $user);
            Session::put('socities', $socities);
            Session::put('acl', $acl);

	       Session::put('role_name', $role_name);
            $arr = array('success' => true,'redirect_url'=>$redirect_url);
            return json_encode($arr);

	}
        
        public function reset_password_session()
        {
            $change_password = \Input::get('change_password',null);
             
            Session::put('user.password_changed', $change_password);
            $arr = array('success' => true);
            return json_encode($arr);
        }
        
        

}
