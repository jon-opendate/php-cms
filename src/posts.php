<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function all_posts(): array
{
    return db()
        ->query('SELECT * FROM posts ORDER BY updated_at DESC, id DESC')
        ->fetchAll();
}

function find_post(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $post = $stmt->fetch();

    return $post ?: null;
}

function save_post(array $input, ?int $id = null): void
{
    $title = trim((string) ($input['title'] ?? ''));
    $slug = slugify((string) ($input['slug'] ?? $title));
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
