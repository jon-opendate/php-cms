<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/redis.php';

function all_posts(): array
{
    return db()
        ->query('SELECT * FROM posts ORDER BY updated_at DESC, id DESC')
        ->fetchAll();
}

function published_posts(): array
{
    return db()
        ->query("SELECT * FROM posts WHERE status = 'published' ORDER BY updated_at DESC, id DESC")
        ->fetchAll();
}

function find_post(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $post = $stmt->fetch();

    return $post ?: null;
}

function find_published_post_by_slug(string $slug): ?array
{
    $stmt = db()->prepare("SELECT * FROM posts WHERE slug = :slug AND status = 'published'");
    $stmt->execute(['slug' => $slug]);
    $post = $stmt->fetch();

    return $post ?: null;
}

function save_post(array $input, ?int $id = null): void
{
    $title = trim((string) ($input['title'] ?? ''));
    $slug = slugify((string) ($input['slug'] ?? '') ?: $title);
    $body = trim((string) ($input['body'] ?? ''));
    $status = ($input['status'] ?? 'draft') === 'published' ? 'published' : 'draft';

    if ($title === '' || $slug === '' || $body === '') {
        throw new InvalidArgumentException('Title, slug, and body are required.');
    }

    if ($id === null) {
        $stmt = db()->prepare(
            'INSERT INTO posts (title, slug, body, status)
             VALUES (:title, :slug, :body, :status)'
        );
        $stmt->execute(compact('title', 'slug', 'body', 'status'));
        return;
    }

    $stmt = db()->prepare(
        'UPDATE posts
         SET title = :title, slug = :slug, body = :body, status = :status, updated_at = NOW()
         WHERE id = :id'
    );
    $stmt->execute([
        'id' => $id,
        'title' => $title,
        'slug' => $slug,
        'body' => $body,
        'status' => $status,
    ]);
}

function delete_post(int $id): void
{
    $stmt = db()->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function slugify(string $value): string
{
    $slug = strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?: '';
    return trim($slug, '-');
}

function record_post_view(int $postId): int
{
    return (int) redis()->incr("post:views:{$postId}");
}

function post_view_count(int $postId): int
{
    return (int) (redis()->get("post:views:{$postId}") ?: 0);
}

function post_view_counts(array $postIds): array
{
    if ($postIds === []) {
        return [];
    }

    $keys = array_map(static fn (int $id): string => "post:views:{$id}", $postIds);
    $values = redis()->mget($keys);

    $out = [];
    foreach ($postIds as $i => $id) {
        $out[(int) $id] = (int) ($values[$i] ?: 0);
    }

    return $out;
}

function forget_post_view_count(int $postId): void
{
    redis()->del("post:views:{$postId}");
}
