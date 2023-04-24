<?php

namespace Avnsh1111\LaravelApiRateLimiter;

use Illuminate\Cache\RateLimiter as CacheRateLimiter;
use Illuminate\Http\Request;

class LaravelApiRateLimiter
{
    protected $cacheRateLimiter;

    public function __construct(CacheRateLimiter $cacheRateLimiter)
    {
        $this->cacheRateLimiter = $cacheRateLimiter;
    }

    public function getKey(Request $request)
    {
        $rateLimitBy = config('api_rate_limiter.rate_limit_by', 'ip');

        switch ($rateLimitBy) {
            case 'ip':
                return $this->rateLimitByIp($request);
            case 'user':
                return $this->rateLimitByUser($request);
            case 'route':
                return $this->rateLimitByRoute($request);
            default:
                throw new \InvalidArgumentException('Invalid rate_limit_by configuration.');
        }
    }

    protected function rateLimitByIp(Request $request)
    {
        return 'rate_limiter:' . $request->ip();
    }

    protected function rateLimitByUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->rateLimitByIp($request);
        }

        return 'rate_limiter:user:' . $user->id;
    }

    protected function rateLimitByRoute(Request $request)
    {
        return 'rate_limiter:route:' . $request->route()->getName();
    }

    public function tooManyAttempts(Request $request, $maxAttempts, $decayMinutes)
    {
        return $this->cacheRateLimiter->tooManyAttempts(
            $this->getKey($request),
            $maxAttempts,
            $decayMinutes
        );
    }

    public function hit(Request $request, $decayMinutes = 1)
    {
        return $this->cacheRateLimiter->hit(
            $this->getKey($request),
            $decayMinutes
        );
    }

    public function remainingAttempts(Request $request, $maxAttempts)
    {
        return $this->cacheRateLimiter->remaining(
            $this->getKey($request),
            $maxAttempts
        );
    }

    public function resetTime(Request $request)
    {
        return $this->cacheRateLimiter->availableIn($this->getKey($request));
    }

    public function isWhitelisted($ip, $userId, $route)
    {
        $whitelist = config('api_rate_limiter.whitelist');

        return in_array($ip, $whitelist['ips']) ||
            in_array($userId, $whitelist['users']) ||
            in_array($route, $whitelist['routes']);
    }

    public function isBlacklisted($ip, $userId, $route)
    {
        $blacklist = config('api_rate_limiter.blacklist');

        return in_array($ip, $blacklist['ips']) ||
            in_array($userId, $blacklist['users']) ||
            in_array($route, $blacklist['routes']);
    }
}
