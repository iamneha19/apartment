<?php
namespace Packages\Admin\Controller;

use App\Http\Controllers\Controller;

class HelpDeskController extends Controller
{
	
	public function index() {
		
		return view('admin::helpdesk.index');
	}
	
}