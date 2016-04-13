<?php 
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

class CalendarController extends Controller{

	public function __construct() {
		//    $this->middleware('auth');
	}
	
	public function index()
	{
		return view('events.index');
		//$content = view('admin::calender.index');
		//return view($this->layout, ['content' => $content]);
	}

}