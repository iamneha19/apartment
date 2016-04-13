<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;

class ChangePasswordController extends Controller{
     
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
      // return view('admin::user.index');
                $email = Session::get('user.email');
		$content = view('admin::user.change_pwd',['user_email'=>$email]);
		return view($this->layout, ['content' => $content]);
    }
}