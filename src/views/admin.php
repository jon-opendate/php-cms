<?php
/**
 * @var array<int,array<string,mixed>> $posts
 * @var array<string,mixed>|null $editing
 * @var string|null $error
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — PHP CMS</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <main class="shell">
        <section class="editor">
            <div>
                <p class="eyebrow"><a href="/">← View site</a></p>
                <h1><?= $editing ? 'Edit post' : 'New post' ?></h1>
            </div>

            <?php if ($error): ?>
                <p class="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= $editing ? '/admin/edit/' . (int) $editing['id'] : '/admin' ?>">
                <label>
                    Title
                    <input name="title" required maxlength="180" value="<?= e((string) ($editing['title'] ?? '')) ?>">
                </label>

                <label>
                    Slug
                    <input name="slug" maxlength="180" placeholder="auto-generated-from-title" value="<?= e((string) ($editing['slug'] ?? '')) ?>">
                </label>

                <label>
                    Body
                    <textarea name="body" required rows="9"><?= e((string) ($editing['body'] ?? '')) ?></textarea>
                </label>

                <label>
                    Status
                    <select name="status">
                        <?php $status = (string) ($editing['status'] ?? 'draft'); ?>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </label>

                <div class="actions">
                    <button type="submit"><?= $editing ? 'Save changes' : 'Create post' ?></button>
                    <?php if ($editing): ?>
                        <a class="button secondary" href="/admin">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="posts" aria-label="Posts">
            <?php if ($posts === []): ?>
                <p class="empty">No posts yet — create one with the form.</p>
            <?php endif; ?>
            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <div>
                        <p class="status"><?= e((string) $post['status']) ?></p>
                        <h2><?= e((string) $post['title']) ?></h2>
                        <p class="slug">
                            <?php if ($post['status'] === 'published'): ?>
                                <a href="/<?= e((string) $post['slug']) ?>">/<?= e((string) $post['slug']) ?></a>
                            <?php else: ?>
                                /<?= e((string) $post['slug']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <p><?= nl2br(e((string) $post['body'])) ?></p>
                    <div class="post-actions">
                        <a class="button secondary" href="/admin/edit/<?= (int) $post['id'] ?>">Edit</a>
                        <form method="post" action="/admin/delete/<?= (int) $post['id'] ?>" onsubmit="return confirm('Delete this post?')">
                            <button class="danger" type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
