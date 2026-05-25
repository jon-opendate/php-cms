<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function view(string $template, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require __DIR__ . '/views/' . $template . '.php';
}
