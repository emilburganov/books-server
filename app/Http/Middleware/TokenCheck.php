<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TokenCheck
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->baseError("Authentication error", 401);
        }

        $user = User::query()->firstWhere('token', $token);

        if (!$user) {
            return $this->baseError("Authentication error", 401);
        }

        Auth::login($user);
        Auth::setUser($user);

        return $next($request);
    }
}
