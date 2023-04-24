<?php

namespace Tests\Feature;

use Orchestra\Testbench\TestCase;
use Avnsh1111\LaravelApiRateLimiter\RateLimiter;
use Illuminate\Http\Request;
use Avnsh1111\LaravelApiRateLimiter\Middleware\RateLimiterMiddleware;

class RateLimiterTest extends TestCase
{
    protected $rateLimiter;
    protected $rateLimiterMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'api_rate_limiter.whitelist' => [
                'ips' => [], // IP addresses to whitelist
                'users' => [], // User IDs to whitelist
                'routes' => [], // Route names to whitelist
            ],
            'api_rate_limiter.blacklist' => [
                'ips' => [], // IP addresses to blacklist
                'users' => [], // User IDs to blacklist
                'routes' => [], // Route names to blacklist
            ],
        ]);
        $this->rateLimiter = app(RateLimiter::class);
        $this->rateLimiterMiddleware = new RateLimiterMiddleware($this->rateLimiter);
    }

    public function testRateLimiterAllowsWhitelistedIpAddress()
    {
        config(['api_rate_limiter.whitelist.ips' => ['127.0.0.1']]);

        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $response = $this->rateLimiterMiddleware->handle($request, function () {
            return response('OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRateLimiterBlocksBlacklistedIpAddress()
    {
        config(['api_rate_limiter.blacklist.ips' => ['127.0.0.1']]);

        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $response = $this->rateLimiterMiddleware->handle($request, function () {
            return response('OK', 200);
        });

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function testRateLimiterBlocksRequestsWithTooManyAttempts()
    {
        config([
            'api_rate_limiter.default_limit' => 2,
            'api_rate_limiter.rate_limit_by' => 'ip',
        ]);

        $ip = '127.0.0.1';

        for ($i = 0; $i < 2; $i++) {
            $request = Request::create('/test', 'GET');
            $request->server->set('REMOTE_ADDR', $ip);

            $response = $this->rateLimiterMiddleware->handle($request, function () {
                return response('OK', 200);
            });

            $this->assertEquals(200, $response->getStatusCode());
        }

        // Exceed the limit
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', $ip);

        $response = $this->rateLimiterMiddleware->handle($request, function () {
            return response('OK', 200);
        });

        $this->assertEquals(429, $response->getStatusCode());
    }
}
