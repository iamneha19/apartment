<?php
namespace Packages\Billing\Controllers;

use App\Http\Controllers\Controller;

class BillingController extends Controller
{
    public function index()
    {
        return view('Billing::index');
    }

    public function create()
    {
        return view('Billing::create');
    }
}
