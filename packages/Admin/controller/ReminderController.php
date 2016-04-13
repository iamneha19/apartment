<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;
class ReminderController extends Controller{
     
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
//        print_r("hello");exit;
        $content = view('admin::reminders.index');
        return view($this->layout,['content'=>$content]);
    }

}



