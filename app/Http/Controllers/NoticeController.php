<?php

namespace App\Http\Controllers;
use HTML2PDF;
use Response;

class NoticeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Notice Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/
        public $layout = 'layouts.default';
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('checkSession');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
                $content = view('notice.index');
                return view($this->layout, ['content' => $content]);
	}

        /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function old()
	{
                $content = view('notice.old');
                return view($this->layout, ['content' => $content]);
	}

        /**
	 * View notice.
	 *
	 * @return Response
	 */
	public function view($id)
	{

        $content = view('notice.view',['id'=>$id]);
        return view($this->layout, ['content' => $content])->render();
	}
        /**
	 * View notice.
	 *
	 * @return Response
	 */
	public function edit($id)
	{
                $content = view('notice.edit',['id'=>$id]);
                return view($this->layout, ['content' => $content]);
	}

}
