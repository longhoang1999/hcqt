<?php

namespace App\Http\Middleware;

use Closure;
use Lang;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $check = false;
        foreach ($roles as $role) {
            if ($user = Sentinel::getUser()){
                if ($user->inRole($role)){
                    $check = true;
                }
            }
        }
        if($check){
            return $next($request);
        }else{
            return back()->with('error',Lang::get('project/Common/title.bkcqtc'));
        }
    }
}
