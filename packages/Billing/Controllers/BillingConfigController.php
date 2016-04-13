<?php

namespace Packages\Billing\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingConfigController extends Controller
{
	public function create()
	{
	   return view('Billing::config.index');
	}
}
