<?php namespace Packages\SuperAdmin\Controller;
use App\Http\Controllers\Controller;
use Session;

class IndexController extends Controller {

  public function __construct() {
//    $this->middleware('auth');
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function index()
  {
	// echo"welcome to homepage..!!";
    return view('superadmin::login');
  }
  
  public function dashboard()
  {
	// echo"welcome to homepage..!!";
    return view('superadmin::home');
  }
  
    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function storeSession()
    {
        $superAdmin = array();
        $superAdmin['access_token'] = \Input::get('access_token',null);
        $superAdmin['user'] = \Input::get('user',null);
        
        Session::put('superadmin', $superAdmin);
        $arr = array('success' => true);
        return json_encode($arr);

    }
    
    public function logout()
    {
        Session::flush();
        return redirect()->route('super_admin.login');
    }
}

