<?php namespace Packages\Admin\Controller;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
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
    if(strtolower(Session::get('role_name'))=='admin')
    {
        if (Session::get('socities.0.is_approved') === 'NO'){
            return redirect(route('import.society.config'));
        }

        else
            return view('admin::helpdesk.index');
    }
    if(strtolower(Session::get('role_name'))=='chairperson' || strtolower(Session::get('role_name'))=='chairman')
    {
        if (Session::get('socities.0.is_approved') === 'NO')
            {
            return redirect(route('admin.chairmanConfig'));
        }

        else
            return view('admin::helpdesk.index');
    }    
    if((strtolower(Session::get('role_name')=='member,chairperson') || strtolower(Session::get('role_name'))=='chairperson,member') || (strtolower(Session::get('role_name'))=='member,chairman' || strtolower(Session::get('role_name'))=='chairman,member')){      
        if (Session::get('socities.0.is_approved') === 'NO'){
            return redirect(route('admin.chairmanConfig'));
        }
         else { 
             return view('admin::helpdesk.index');
        }
    }
    
  }
//     if(Session::get('role_name')=='Chairperson' || Session::get('role_name')=='Chairman')
//    {
//     print_r(Session::get('socities'));exit;    
//        if (Session::get('socities.0.is_approved') == 'YES'){
//            print_r("hello1243");exit;
//            return redirect(route('admin.chairmanConfig'));
//        }
//
//        else
//            return view('admin::helpdesk.index');
//    }
//  }
  
  public function complex() {
  	
  	return view('admin::complex.index');
  }
}

