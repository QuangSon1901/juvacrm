<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('auth.login');
        }
        
        // Super Admin có mọi quyền
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        if (!$user->hasPermission($permission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền thực hiện thao tác này.'
                ], 403);
            }
            
            return abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
        
        return $next($request);
    }
}