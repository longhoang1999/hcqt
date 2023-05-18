<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Sentinel;

class SentinelOperator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Sentinel::check()) {
            return redirect('admin/signin')->with('info', 'You must be logged in!');
        } elseif (
            !Sentinel::inRole('admin') && 
            !Sentinel::inRole('operator')  && 
            !Sentinel::inRole('canbo')  && 
            !Sentinel::inRole('ns_thuchien')  && 
            !Sentinel::inRole('ns_phutrach')  && 
            !Sentinel::inRole('ns_kiemtra')
        ) {
            return redirect('my-account');
        }

        $tasks_count = Task::where('user_id', Sentinel::getUser()->id)->count();
        $request->attributes->add(['tasks_count' => $tasks_count]);

        return $next($request);
    }
}
