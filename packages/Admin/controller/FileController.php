<?php namespace Packages\Admin\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Session;

class FileController extends Controller{
     
    public function __construct() {
    //    $this->middleware('auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public $layout = 'admin::layouts.admin_layout';
    public function common()
    {
	  $content = view('admin::file.common');
	  return view($this->layout,['content'=>$content]);
    }
    
    public function flat()
    {
	  $content = view('admin::file.flat ');
	  return view($this->layout,['content'=>$content]);
    }
    
    public function agreement()
    {
	  $content = view('admin::file.agreement');
	  return view($this->layout,['content'=>$content]);
    }
    
    public function fileList($folder_id)
    {
	  $content = view('admin::file.list',['folder_id'=>$folder_id]);
	  return view($this->layout,['content'=>$content]);
    }
    
    public function editSocietyDocument($id)
    {
	  $content = view('admin::file.societyFileEdit',['id'=>$id]);
	  return view($this->layout,['content'=>$content]);
    }
    
    public function edit($file_id)
    {
	  $content = view('admin::file.edit',['file_id'=>$file_id]);
	  return view($this->layout,['content'=>$content]);
    }
    
    public function societyFiles()
    {
        $createPermission = false;  // To check society_document.create permission  
        $listPermission = false; // To check society_document.list permission
        
        if(session()->get('acl.admin.admin_files')){
            $permissions = Session::get('acl.admin.admin_files.permissions');
            if(is_array($permissions)){
                if(in_array('society_document.list', $permissions)){
                    $listPermission = true;
                }
                
                if(in_array('society_document.create', $permissions)){
                    $createPermission = true;
                } 
            }
        }else{
            
        }
        
        $viewData['createPermission'] = $createPermission;
        $viewData['listPermission'] = $listPermission;
        
        $content = view('admin::file.societyFiles',$viewData);
        return view($this->layout,['content'=>$content]);
        
    }
    public function FlatDocument()
    {
        $content = view('admin::file.flatDocuments');
	  return view($this->layout,['content'=>$content]);
    }
    public function FlatDocumentFiles($folder_id)
    {
        $content = view('admin::file.flatFiles',['folder_id'=>$folder_id]);
	  return view($this->layout,['content'=>$content]);
    }
    
    public function societyFileReport()
    {
        $content = view('admin::file.societyFilesReport');
	  return view($this->layout,['content'=>$content]);
    }
    
}



