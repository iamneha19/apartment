<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;

class OfficialCommController extends Controller{
	public function __construct() {
		//    $this->middleware('auth');
	}

	public function index()
	{
		return view('admin::officialcommunication.index');
	}
}
