<?php

namespace Avnsh1111\LaravelApiRateLimiter\Middleware;

use Closure;
use Avnsh1111\LaravelApiRateLimiter\LaravelApiRateLimiter;
use Illuminate\Http\Response;

class RateLimiterMiddleware
{
    protected $rateLimiter;


    public function __construct(LaravelApiRateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function handle($request, Closure $next)
    {
        $maxAttempts = config('api_rate_limiter.default_limit', 60);
        $decayMinutes = 1;

        $ip = $request->ip();
        $userId = $request->user() ? $request->user()->id : null;
        $route = $request->route() ? $request->route()->getName() : null;

        if ($this->rateLimiter->isWhitelisted($ip, $userId, $route)) {
            return $next($request);
        }

        if ($this->rateLimiter->isBlacklisted($ip, $userId, $route)) {
            return response('Too many requests.', 429);
        }

        if ($this->rateLimiter->tooManyAttempts($request, $maxAttempts, $decayMinutes)) {
            $response = new Response('Too Many Attempts', 429);

            if (config('api_rate_limiter.response_headers', true)) {
                $headers = [
                    'X-RateLimit-Limit' => $maxAttempts,
                    'X-RateLimit-Remaining' => $this->rateLimiter->remainingAttempts($request, $maxAttempts),
                    'X-RateLimit-Reset' => $this->rateLimiter->resetTime($request),
                ];

                $response->headers->add($headers);
            }

            return $response;
        }

        $response = $next($request);

        if (config('api_rate_limiter.response_headers', true)) {
            $headers = [
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => $this->rateLimiter->remainingAttempts($request, $maxAttempts) - 1,
                'X-RateLimit-Reset' => $this->rateLimiter->resetTime($request),
            ];

            $response->headers->add($headers);
        }

        $this->rateLimiter->hit($request, $decayMinutes);

        return $response;
    }


}
