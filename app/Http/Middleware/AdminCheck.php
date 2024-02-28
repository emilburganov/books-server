<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->is_admin) {
            return $next($request);
        }

        return $this->baseError('Access denied', 403);
    }
}
