<?php
namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Http\Request;

class CheckSession
{
	public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();
        $routesToAuthenticate = array(
            'admin.acl'=>'acl.admin.admin_acl',
            'admin.users'=>'acl.admin.manage_user'
            );
        if (Session::has('user'))
        {
            $password_changed = Session::get('user.password_changed');
                        
            // Check if user is not admin url and if user not have admin rights then redirect
            if((! session()->has('acl.admin')) and str_contains($request->url(), '/admin'))
            {
               // return redirect()->route('conversations');
            }
            
            // if user is not dashboard with insufficient previlage then logout and redirect
//            if((! session()->has('acl.resident')) and str_contains($request->url(), '/dashboard'))
//            {
//                session()->flush();
//                dd('exit');
//                return redirect()->back();
//            }

            if($password_changed)
            {
                if(array_key_exists($routeName, $routesToAuthenticate)){
                    if(!session()->has($routesToAuthenticate[$routeName])){
                        return redirect()->intended('/');
                    }else{
                        return $next($request);
                    }
                }else{
                   return $next($request); 
                }
                
            } else
            {
                
                if( ($routeName != 'reset_password') && ($routeName != 'logout') )
                {
                   return redirect()->route('reset_password');
                }else
                {
                    return $next($request);
                }
            }

            return $next($request);
        }

        return redirect()->intended('/');
	}

}
