<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFirstPersonExists
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->people()->count() === 0) {
            return redirect()->route('welcome.first');
        }

        return $next($request);
    }
}

