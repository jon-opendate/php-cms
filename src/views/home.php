<?php
/**
 * @var array<int,array<string,mixed>> $posts
 * @var array<int,int> $views
 */
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
    <main class="site">
        <header class="site-header">
            <p class="eyebrow">PostgreSQL + Redis backed · <a href="/health">health</a></p>
            <h1>PHP CMS</h1>
        </header>

        <?php if ($posts === []): ?>
            <p class="empty">No posts published yet. Visit <a href="/admin">/admin</a> to create one.</p>
        <?php else: ?>
            <ul class="post-list">
                <?php foreach ($posts as $post): ?>
                    <li>
                        <article>
                            <h2><a href="/<?= e((string) $post['slug']) ?>"><?= e((string) $post['title']) ?></a></h2>
                            <p class="meta">
                                Updated <?= e((string) $post['updated_at']) ?>
                                · <?= (int) ($views[(int) $post['id']] ?? 0) ?> views
                            </p>
                            <p class="excerpt"><?= e(mb_strimwidth((string) $post['body'], 0, 220, '…')) ?></p>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
</body>
</html>
