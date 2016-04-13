<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;

class SocietyController extends Controller{

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
        $society_id = Session::get('user.society_id');
		$content = view('admin::society.index',['society_id'=>$society_id]);
		return view($this->layout, ['content' => $content]);
    }
}
