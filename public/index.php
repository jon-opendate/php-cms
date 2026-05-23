<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/posts.php';

$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'delete' && $id !== null) {
            delete_post($id);
        } elseif ($action === 'edit' && $id !== null) {
            save_post($_POST, $id);
        } else {
            save_post($_POST);
        }

        header('Location: /');
        exit;
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$editing = $action === 'edit' && $id !== null ? find_post($id) : null;
$posts = all_posts();

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP CMS</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <main class="shell">
        <section class="editor">
            <div>
                <p class="eyebrow">PostgreSQL backed</p>
                <h1>PHP CMS</h1>
            </div>

            <?php if ($error): ?>
                <p class="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= $editing ? '/?action=edit&id=' . (int) $editing['id'] : '/' ?>">
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
                        <a class="button secondary" href="/">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="posts" aria-label="Posts">
            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <div>
                        <p class="status"><?= e((string) $post['status']) ?></p>
                        <h2><?= e((string) $post['title']) ?></h2>
                        <p class="slug">/<?= e((string) $post['slug']) ?></p>
                    </div>
                    <p><?= nl2br(e((string) $post['body'])) ?></p>
                    <div class="post-actions">
                        <a class="button secondary" href="/?action=edit&id=<?= (int) $post['id'] ?>">Edit</a>
                        <form method="post" action="/?action=delete&id=<?= (int) $post['id'] ?>" onsubmit="return confirm('Delete this post?')">
                            <button class="danger" type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
