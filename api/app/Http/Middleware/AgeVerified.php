<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AgeVerified
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->cookie('age_verified') !== '1') {
            return redirect()->route('age.check');
        }

        return $next($request);
    }
}
