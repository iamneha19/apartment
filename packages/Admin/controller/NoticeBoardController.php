<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;

class NoticeBoardController extends Controller {

    public $layout = 'admin::layouts.admin_layout';

	public function __construct()
	{
		$this->middleware('checkSession');
	}

	public function index()
	{
                $content = view('admin::notice.index');
                return view($this->layout, ['content' => $content]);
	}

	public function old()
	{
                $content = view('admin::notice.old');
                return view($this->layout, ['content' => $content]);
	}

	public function view($id)
	{
		$content = view('admin::notice.view',['id'=>$id]);
        return view($this->layout, ['content' => $content]);
	}
	
	public function viewOldNotice($id)
	{	
		$content = view('admin::notice.viewOldNotice',['id'=>$id]);
        return view($this->layout, ['content' => $content]);
	}

	public function edit($id)
	{
                $content = view('admin::notice.edit',['id'=>$id]);
                return view($this->layout, ['content' => $content]);
	}
}
