<?php

namespace App\Http\Middleware;

use App\Models\AllowedIp;
use Closure;
use Illuminate\Http\Request;

class AclIp
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $ipAddress = $request->ip();
        if (AllowedIp::where([['ip_address', $ipAddress], ['disallowed', false]])->first() == null) {
            die('Not allowed ' . $ipAddress);
        }
        return $next($request);
    }
}
