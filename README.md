# Laravel API Rate Limiter

Laravel API Rate Limiter is an open-source package designed to help developers easily implement and manage rate limiting for their API endpoints in Laravel applications. This package aims to provide a flexible and configurable solution for protecting APIs from excessive requests, ensuring optimal performance and preventing abuse.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Add-ons](#add-ons)
- [Support](#support)
- [Contributing](#contributing)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require avnsh1111/laravel-api-rate-limiter
```

## Configuration

Before publishing the configuration file, you need to add the `LaravelApiRateLimiterServiceProvider` to the `providers` array in your `config/app.php`:

```php
'providers' => [
    // ...
    Avnsh1111\LaravelApiRateLimiter\LaravelApiRateLimiterServiceProvider::class,
],
```

After adding the ServiceProvider, publish the configuration file to configure the rate limiting settings:

```bash
php artisan vendor:publish --provider="Avnsh1111\LaravelApiRateLimiter\LaravelApiRateLimiterServiceProvider"
```

This will create a `laravel-api-rate-limiter.php` configuration file in your `config` folder.

In the configuration file, you can set the default rate limit, rate limiting method, response headers, and whitelist/blacklist specific IP addresses, users, and routes.

```php
return [
    'default_limit' => 60, // default number of requests allowed per minute

    'rate_limit_by' => 'ip', // rate limit by 'ip', 'user', or 'route'

    'response_headers' => true, // include rate limit headers in responses

    'whitelist' => [
        'ips' => [], // IP addresses to whitelist
        'users' => [], // User IDs to whitelist
        'routes' => [], // Route names to whitelist
    ],
    'blacklist' => [
        'ips' => [], // IP addresses to blacklist
        'users' => [], // User IDs to blacklist
        'routes' => [], // Route names to blacklist
    ],

];
```

## Usage

To apply rate limiting to specific routes or route groups, use the provided middleware:

```php
use Avnsh1111\LaravelApiRateLimiter\Middleware\RateLimiter;

Route::middleware([RateLimiter::class])->group(function () {
    Route::get('/api/endpoint', 'ApiController@index');
});
```

You can also customize the rate limit for specific routes by passing the limit as an argument to the middleware:

```php
Route::get('/api/endpoint', 'ApiController@index')->middleware(RateLimiter::class . ':100'); // 100 requests per minute
```

## Add-ons

1. Integration with Laravel's built-in caching system to store and manage rate limiting data efficiently.
2. A user-friendly dashboard for monitoring rate limiting statistics, such as the number of requests, blocked requests, and whitelisted/blacklisted IPs or users.
3. Support for automatically adjusting rate limits based on server load or other performance metrics.
4. Integration with popular third-party API management and monitoring tools, such as Postman or Swagger, to provide additional insights and control over API usage.

## Support

If you encounter any issues or require assistance, please check the comprehensive documentation or reach out to the community through the issue tracker on GitHub.

## Contributing

Contributions are welcome! If you would like to contribute to the project, please fork the repository, make your changes, and submit a pull request. Be sure to follow the coding standards and provide tests for any new features or bug fixes.

## License

The Laravel API Rate Limiter package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
