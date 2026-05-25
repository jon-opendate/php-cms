<?php

declare(strict_types=1);

require_once __DIR__ . '/posts.php';
require_once __DIR__ . '/render.php';

function admin_dispatch(array $segments, string $method): void
{
    $error = null;
    $editing = null;

    if ($method === 'POST') {
        try {
            if (($segments[0] ?? null) === 'delete' && isset($segments[1])) {
                delete_post((int) $segments[1]);
            } elseif (($segments[0] ?? null) === 'edit' && isset($segments[1])) {
                save_post($_POST, (int) $segments[1]);
            } else {
                save_post($_POST);
            }

            header('Location: /admin');
            return;
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }

    if (($segments[0] ?? null) === 'edit' && isset($segments[1])) {
        $editing = find_post((int) $segments[1]);
    }

    view('admin', [
        'posts' => all_posts(),
        'editing' => $editing,
        'error' => $error,
    ]);
}
