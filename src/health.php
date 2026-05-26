<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/redis.php';

function health_report(): array
{
    return [
        'postgres' => check_postgres(),
        'redis' => check_redis(),
    ];
}

function check_postgres(): array
{
    try {
        $value = db()->query('SELECT 1')->fetchColumn();
        if ((int) $value !== 1) {
            return ['ok' => false, 'error' => 'unexpected result from SELECT 1'];
        }
        return ['ok' => true];
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}

function check_redis(): array
{
    try {
        $pong = redis()->ping();
        $ok = $pong === true || $pong === '+PONG' || $pong === 'PONG';
        if (!$ok) {
            return ['ok' => false, 'error' => 'unexpected PING reply: ' . var_export($pong, true)];
        }
        return ['ok' => true];
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}
