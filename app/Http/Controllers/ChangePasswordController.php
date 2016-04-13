<?php namespace App\Http\Controllers;
use Session;
class ChangePasswordController extends Controller {
     
    public function __construct() {
        $this->middleware('checkSession');
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
	public $layout = 'layouts.default';
    
	public function index()
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