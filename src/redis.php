<?php

declare(strict_types=1);

function redis(): Redis
{
    static $client = null;

    if ($client instanceof Redis) {
        return $client;
    }

    $host = getenv('REDIS_HOST') ?: 'localhost';
    $port = (int) (getenv('REDIS_PORT') ?: 6379);

    $client = new Redis();
    if (!@$client->connect($host, $port, 1.5)) {
        throw new RuntimeException("Could not connect to Redis at {$host}:{$port}");
    }

    return $client;
}
