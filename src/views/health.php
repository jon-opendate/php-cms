<?php
/**
 * @var array{postgres: array{ok: bool, error?: string}, redis: array{ok: bool, error?: string}} $checks
 * @var bool $ok
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Health — PHP CMS</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <main class="site">
        <header class="site-header">
            <p class="eyebrow"><a href="/">← Back to site</a></p>
            <h1>Health</h1>
            <p class="meta">Overall: <strong class="<?= $ok ? 'health-ok' : 'health-fail' ?>"><?= $ok ? 'OK' : 'DEGRADED' ?></strong></p>
        </header>

        <ul class="health-list">
            <?php foreach ($checks as $name => $check): ?>
                <li class="health-row">
                    <span class="health-name"><?= e($name) ?></span>
                    <span class="health-status <?= $check['ok'] ? 'health-ok' : 'health-fail' ?>">
                        <?= $check['ok'] ? 'OK' : 'FAIL' ?>
                    </span>
                    <?php if (!$check['ok'] && isset($check['error'])): ?>
                        <p class="health-error"><?= e((string) $check['error']) ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
