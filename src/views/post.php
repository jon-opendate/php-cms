<?php /** @var array<string,mixed> $post */ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e((string) $post['title']) ?> — PHP CMS</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <main class="site">
        <p class="back"><a href="/">← All posts</a></p>
        <article class="single">
            <header>
                <h1><?= e((string) $post['title']) ?></h1>
                <p class="meta">Updated <?= e((string) $post['updated_at']) ?></p>
            </header>
            <div class="body">
                <?php foreach (preg_split('/\R{2,}/', (string) $post['body']) as $paragraph): ?>
                    <p><?= nl2br(e($paragraph)) ?></p>
                <?php endforeach; ?>
            </div>
        </article>
    </main>
</body>
</html>
