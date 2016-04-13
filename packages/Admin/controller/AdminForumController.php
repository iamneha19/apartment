<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;

class AdminForumController extends Controller{
	public function __construct() {
		//    $this->middleware('auth');
	}

	public function index()
	{
//		dd("be");
		return view('admin::pages.adminforum');
		//$content = view('admin::calender.index');
		//return view($this->layout, ['content' => $content]);
	}
    public function forum_reply($id, $page, $search=null)
    {
		if ($search === null)
			$search = "";
        return view('admin::pages.forum_reply',['id'=>$id, 'page'=>$page, 'search'=>$search]);
        
    }
	
	public function backforums($page, $search=null)
	{
		if ($search === null)
			$search = "";
		return view('admin::pages.adminforum', ['page'=>$page, 'search'=>$search]);
	}


/* 	public function topicList(){

		$topiclist = topic::all();
		//return $topiclist;
		return view('adminforum',['topiclist'=>$topiclist]);

	} */


}
