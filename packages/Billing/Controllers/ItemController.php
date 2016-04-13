<?php namespace Packages\Billing\Controllers;

use App\Http\Controllers\Controller;

class ItemController extends Controller
{
    public function index()
    {
        return view('Billing::item.index');
    }

    public function create()
    {
        return view('Billing::item.create');
    }
}
