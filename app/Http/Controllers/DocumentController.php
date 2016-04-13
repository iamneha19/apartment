<?php namespace App\Http\Controllers;
use Config;
use Session;
class DocumentController extends Controller {

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
	 * List folders.
	 *
	 * @return view
	 */
	public function index()
	{
                $content = view('document.index');
                return view($this->layout, ['content' => $content]);
	}
        
        /**
	 * List resident files.
	 *
	 * @return view
	 */
//	public function residentFiles($id)
//	{
//                $content = view('document.resident_files',['folder_id'=>$id]);
//                return view($this->layout, ['content' => $content]);
//	}
    
    public function residentFiles()
	{
                $flat_id = Session::get('user.flat_id');
                $content = view('document.resident_files',['flat_id'=>$flat_id]);
                return view($this->layout, ['content' => $content]);
	}
        
        /**
	 * List resident files.
	 *
	 * @return view
	 */
	public function officialFiles($id)
	{
                $content = view('document.official_files',['folder_id'=>$id]);
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
        /*
         * List of flat specific documents
         * 
         * @ return view
         */
    public function EditFlatDocument($file_id)
        {
              $content = view('document.document_edit',['file_id'=>$file_id]);
              return view($this->layout,['content'=>$content]);
        }
        /**
	 * Download files.
	 *
	 * @return view
	 */
	public function download()
        {
            $file = \Input::get('file');
            $name = \Input::get('name');

            $FileName = $file;
            header('Content-disposition: attachment; filename="'.$name.'"');
            readfile($FileName);
        }
		
	  public function downloadotherfile()
        {
            $file = \Input::get('file');
        	$http_path = getenv('API_URL').'uploads/amenities/';
			$FileName =$http_path. $file;
			header('Content-disposition: attachment; filename="'.$file.'"');
            readfile($FileName);
        }

}
