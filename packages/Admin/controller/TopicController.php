<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;

class TopicController extends Controller{
	public function __construct() {
		//    $this->middleware('auth');
	}

	public function index()
	{
		return view('admin::pages.topic');
		//$content = view('admin::calender.index');
		//return view($this->layout, ['content' => $content]);
	}

}