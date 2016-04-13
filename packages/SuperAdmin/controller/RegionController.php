<?php

namespace Packages\SuperAdmin\Controller;

use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    public $layout = 'superadmin::layouts.super_admin_layout';

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index()
    {
        $content = view('superadmin::region.index');
        return view($this->layout, ['content' => $content]);
    }
}
