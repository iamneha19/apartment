<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;

class CalenderController extends Controller{
	public function __construct() {
		//    $this->middleware('auth');
	}

	public function index()
	{
		return view('admin::pages.calender');
		//$content = view('admin::calender.index');
		//return view($this->layout, ['content' => $content]);
	}
	
}