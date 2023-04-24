<?php

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
