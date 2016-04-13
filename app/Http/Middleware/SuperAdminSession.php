<?php
namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Http\Request;

class SuperAdminSession
{
	public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();
        if (Session::has('superadmin.user'))
        {
            return $next($request);
        }else{
        
            if( ($routeName == 'super_admin.login' || $routeName == 'super_admin.storesession'))
            {
               return $next($request);
            }else
            {
                return redirect()->intended('/');
            }
        }

        
	}

}
