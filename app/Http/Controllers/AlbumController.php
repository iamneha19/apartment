<?php namespace App\Http\Controllers;

class AlbumController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Document Controller
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
	 * List albums.
	 *
	 * @return view
	 */
	public function index()
	{
                $content = view('album.index');
                return view($this->layout, ['content' => $content]);
	}
        
        /**
	 * List album photos.
	 *
	 * @return view
	 */
	public function photos($id = null)
	{
                $content = view('album.photos',['album_id'=>$id]);
                return view($this->layout, ['content' => $content]);
	}
        
        /**
	 * List folders.
	 *
	 * @return view
	 */
	public function upload()
	{
                $content = view('album.upload');
                return view($this->layout, ['content' => $content]);
	}
        
        /**
	 * List folders.
	 *
	 * @return view
	 */
	public function edit($file_id)
        {
              $content = view('document.edit',['file_id'=>$file_id]);
              return view($this->layout,['content'=>$content]);
        }

}
