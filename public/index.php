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

if ($segments === []) {
    view('home', ['posts' => published_posts()]);
    return;
}

if (count($segments) === 1) {
    $post = find_published_post_by_slug($segments[0]);
    if ($post !== null) {
        view('post', ['post' => $post]);
        return;
    }
}

http_response_code(404);
view('not_found');
