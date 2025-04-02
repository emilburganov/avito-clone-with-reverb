<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenCheck
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->unauthorizedError();
        }

        $user = User::query()->firstWhere('token', $token);

        if (!$user) {
            return $this->unauthorizedError();
        }

        auth()->login($user);

        return $next($request);
    }
}
