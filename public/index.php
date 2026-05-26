<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/posts.php';
require_once __DIR__ . '/../src/render.php';

$path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$segments = $path === '' ? [] : explode('/', $path);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (($segments[0] ?? null) === 'admin') {
    require __DIR__ . '/../src/admin.php';
    admin_dispatch(array_slice($segments, 1), $method);
    return;
}

if ($segments === ['health']) {
    require __DIR__ . '/../src/health.php';
    $checks = health_report();
    $ok = $checks['postgres']['ok'] && $checks['redis']['ok'];
    http_response_code($ok ? 200 : 503);
    view('health', ['checks' => $checks, 'ok' => $ok]);
    return;
}

if ($segments === []) {
    $posts = published_posts();
    $views = post_view_counts(array_map(static fn (array $p): int => (int) $p['id'], $posts));
    view('home', ['posts' => $posts, 'views' => $views]);
    return;
}

if (count($segments) === 1) {
    $post = find_published_post_by_slug($segments[0]);
    if ($post !== null) {
        $views = record_post_view((int) $post['id']);
        view('post', ['post' => $post, 'views' => $views]);
        return;
    }
}

http_response_code(404);
view('not_found');
